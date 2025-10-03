<?php
namespace SOM;

use MapasCulturais\API;
use MapasCulturais\App;
use MapasCulturais\i;


// class Theme extends \Subsite\Theme {
class Theme extends \MapasCulturais\Themes\BaseV2\Theme {

    static function getThemeFolder() {
        return __DIR__;
    }

    function _init() {
        parent::_init();

        $app = App::i();

        /* REMOVE A VALIDAÇÃO DA ÁREA DE ATUAÇÃO */
        $app->hook('entity(Agent).validationErrors', function (&$errors) {
            unset($errors['term-area']);
        });

        /* ADICIONA A ÁREA DE ATUAÇÃO MÚSICA */
        $app->hook('entity(Agent).<<insert|save>>:before', function() {
            if (!in_array(i::__('Música'), $this->terms['area'])) {
                $terms = $this->terms ?? [];
                $terms['area'] = $terms['area'] ?? [];
                $terms['area'][] = i::__('Música');
                $this->terms = $terms;
            }
        });

        /* RESTRINGE PERMISSÕES PARA MANIPULAR OPORTUNIDADES */
        $app->hook('can(Opportunity.<<*>>)', function() use ($app) {
            return $app->auth->isUserAuthenticated() && $app->user->isUserAdmin($app->user);
        });

        /* FILTRA A API DE AGENTES */
        $app->hook('ApiQuery(Agent).joins', function(&$joins) use($app) {
            $request = $app->request;

            // Don't filter agents when listing users, editing profile, etc.
            $listing_agents = $request->controllerId === 'agent' && $request->action === 'find';

            if(!$listing_agents) {
                return;
            }

            $joins .= "
                JOIN e.user u
                JOIN u.__metadata um WITH um.key = 'som_active' AND um.value = '1'
                JOIN e.__termRelations tr
                JOIN tr.term funcao WITH funcao.taxonomy = 'funcao_musica' AND funcao.term IS NOT NULL
            ";
        });

        $app->hook('ApiQuery(Agent).params', function(&$params) use($app) {
            $funcoes_validas = ['artista', 'produtor'];
            $funcao = $params['@funcao'] ?? false;

            if ($funcao && in_array($funcao, $funcoes_validas)) {
                $taxonomy = $app->getRegisteredTaxonomyBySlug('funcao_musica');
                $producers_terms = [i::__('Produtor'),i::__('Produtor de Festival'),i::__('Produtor de Campo')];

                $terms = [
                    'artista' => array_diff($taxonomy->restrictedTerms, $producers_terms),
                    'produtor' => $producers_terms,
                ];
                 
                
                $filter = API::IN($terms[$funcao]);

                if(isset($params['term:funcao_musica'])) {
                    $params['term:funcao_musica'] = API::AND($params['term:funcao_musica'], $filter);
                } else {
                    $params['term:funcao_musica'] = $filter;
                }
                
                unset($params['@funcao']);
            }

        });

        /* DEFINE O METADADO som_active = 1 NO LOGIN  */
        $app->hook('auth.successful', function () use ($app) {
            if ($app->auth->isUserAuthenticated()) {
                $user = $app->user;

                if (!$user->som_active) {
                    $user->som_active = '1';
                    $user->save(true);
                }
            }
        });

        /* REDIRECIONA O AGENTE PARA A PÁGINA DO PERFIL SE O PERFIL NÃO ESTIVER COMPLETO */
        $app->hook('GET(<<*>>):before', function () use ($app) {
            $allowed_routes = [
                'agent' => 'unlock,edit',
                'lgpd' => 'accept',
                'auth' => '*'
            ];

            foreach ($allowed_routes as $controller_id => $actions) {
                if($this->id == $controller_id) {
                    if($actions == '*') {
                        return;
                    }

                    if(in_array($this->action, explode(',', $actions))) {
                        return;
                    }
                }
            }

            if ($app->auth->isUserAuthenticated()) {
                $profile = $app->user->profile;
                if ($profile->validationErrors) {
                    $app->redirect($profile->editUrl);
                }
            }
        });

        /* ADICIONA FUNÇÃO NA MÚSICA NA PSEUDO_QUERY DO AGENTE */
        $app->hook('search-agents-initial-pseudo-query', function (&$pseudo_query) {
            $pseudo_query['term:funcao_musica'] = [];
        });

        $this->assetManager->publishFolder('custom-fonts');

        $addTaxonomyToAgentSingle = function () {
        ?>
            <entity-terms :entity="entity" taxonomy="funcao_musica" classes="col-12" hide-required title="<?php i::esc_attr_e('Função na música');?>"></entity-terms>
        <?php
        };
        $app->hook('template(agent.single.single1-entity-info-taxonomie-area):before', $addTaxonomyToAgentSingle);
        $app->hook('template(agent.single.single2-entity-info-taxonomie-area):before', $addTaxonomyToAgentSingle);

        /* REGISTRO O ÍCONE DO WIDGET DE COMUNIDADES */
        $app->hook('component(mc-icon).iconset', function(&$iconset) {
            $iconset['hand'] = 'ion:hand-right';
        });


        /* ================ NOVAS ROTAS =================== */
        // Cria rota para carregar os produtores
        $app->hook('GET(search.producers)', function() use ($app) {
            $this->render('producers');
        });

        $app->hook('GET(search.artists)', function() use ($app) {
            $this->render('artists');
        });
    }
}

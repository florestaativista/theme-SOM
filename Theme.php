<?php
namespace SOM;

use MapasCulturais\API;
use MapasCulturais\App;
use MapasCulturais\Controllers;
use MapasCulturais\Entities;
use MapasCulturais\i;


// class Theme extends \Subsite\Theme {
class Theme extends \MapasCulturais\Themes\BaseV2\Theme {

    static $requiredAgentFields = [
        'emailPrivado',
        'telefone1',
        'En_Pais',
        'En_Estado',
        'En_Municipio',
    ];

    static $requiredAgent1Fields = [
        'nomeCompleto',
        'dataDeNascimento',
    ];

    static function getThemeFolder() {
        return __DIR__;
    }

    function _init() {
        parent::_init();

        $self = $this;
        $app = App::i();

        /* OBRIGATORIEDADE DOS CAMPOS DOS AGENTES */
        $app->hook('entity(Agent).update:before', function () use($self) {
            /** @var Entities\Agent $this */
            if ($this->type->id == 1) {
                $self->agentRequiredProperties(self::$requiredAgent1Fields);
            }

            $self->agentRequiredProperties(self::$requiredAgentFields);
        });


        $app->hook('GET(agent.edit):before', function () use($self) {
            /** @var Controllers\Agent $this */
            
            $agent = $this->requestedEntity;

            if ($agent->type->id == 1) {
                $self->agentRequiredProperties(self::$requiredAgent1Fields);
            }

            $self->agentRequiredProperties(self::$requiredAgentFields);
        });

        /* ALTERA O TIPO DE REQUISIÇÃO DO SALVAMENTO DE AGENTES PARA PUT */
        $app->hook('view(agent.edit).updateMethod', function(&$update_method) {
            $update_method = 'PUT';
        });

        /* REMOVE A VALIDAÇÃO DA ÁREA DE ATUAÇÃO */
        $app->hook('entity(Agent).validationErrors', function (&$errors) {
            /** @var Entities\Agent $this */

            unset($errors['term-area']);

            if($this->type->id == 1) {
                foreach(self::$requiredAgent1Fields as $field) {
                    if(!$this->$field && !isset($errors[$field])) {
                        $errors[$field] = [i::__('campo obrigatório')];
                    }
                }
            }

            foreach(self::$requiredAgentFields as $field) {
                if(!$this->$field && !isset($errors[$field])) {
                    $errors[$field] = [i::__('campo obrigatório')];
                }
            }

            if (!$this->avatar) {
                $errors['file:avatar'] = [i::__('campo obrigatório')];
            }
        });

        /* ADICIONA A ÁREA DE ATUAÇÃO MÚSICA */
        $app->hook('entity(Agent).<<insert|save>>:before', function() {
            $terms = $this->terms ?: [];
            $terms['area'] = $terms['area'] ?? [];
            if (!in_array(i::__('Música'), $terms['area'])) {
                $terms['area'][] = i::__('Música');
                $this->terms = (array) $terms;
            }
        });

        /* RESTRINGE PERMISSÕES PARA CRIAR OPORTUNIDADES */
        $app->hook('POST(opportunity.index):before', function() use ($app) {
            /**
             * @var Controllers\Opportunity $this
             */
            if (!$app->user->is('admin')) {
                $this->errorJson(i::__('Permissão negada'), 403);
            }
        });

        /* RESTRINGE VISUALIZAÇÃO DO BOTÃO DE CRIAÇÃO DE OPORTUNIDADE */
        $app->hook('component(create-opportunity):params', function (&$component_name) use ($app) {
            if (!$app->user->is('admin')) {
                $component_name = 'mc-empty';
            }
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

        $app->hook('ApiQuery(Agent).params', function(&$params) {
            $funcoes_validas = ['artista', 'produtor'];
            $funcao = $params['@funcao'] ?? false;

            if ($funcao && in_array($funcao, $funcoes_validas)) {
                $terms = [
                    'artista' => [i::__('Artista')],
                    'produtor' => [i::__('Produção')],
                ];

                $filter = API::IN($terms[$funcao]);
                $params['term:funcao_musica'] = $filter;

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
        $app->hook('GET(search.producers)', function() {
            $this->render('producers');
        });

        $app->hook('GET(search.artists)', function() {
            $this->render('artists');
        });

        /* ALTERA OS TÍTULOS DAS PÁGINAS */
        $app->hook('view.title(search.artists)', function(&$title) {
            $title = i::__('Artistas');
        });
        $app->hook('view.title(search.producers)', function(&$title) {
            $title = i::__('Produção');
        });
    }

    function agentRequiredProperties(array $required_metadata) {
        $app = App::i();

        $metadata = $app->getRegisteredMetadata('MapasCulturais\Entities\Agent');

        foreach($metadata as $meta) {
            if(in_array($meta->key, $required_metadata)) {
                $meta->is_required = true;
            }
        }
    }
}

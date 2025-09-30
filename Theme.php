<?php
namespace SOM;

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
        $app->hook('mapasculturais.body:after', function() use ($app) {
            $this->part('theme-css');
        });

        $addTaxonomyToAgentEdit = function () {
        ?>
            <entity-terms :entity="entity" taxonomy="funcao_musica" editable classes="col-12" title="<?php i::_e('Função na música'); ?>"></entity-terms>
        <?php
        };
        $app->hook('template(agent.edit.edit1-entity-info-taxonomie-area):before', $addTaxonomyToAgentEdit);
        $app->hook('template(agent.edit.edit2-entity-info-taxonomie-area):before', $addTaxonomyToAgentEdit);

        $addTaxonomyToAgentSingle = function () {
        ?>
            <entity-terms :entity="entity" taxonomy="funcao_musica" classes="col-12" hide-required title="<?php i::esc_attr_e('Função na música');?>"></entity-terms>
        <?php
        };
        $app->hook('template(agent.single.single1-entity-info-taxonomie-area):before', $addTaxonomyToAgentSingle);
        $app->hook('template(agent.single.single2-entity-info-taxonomie-area):before', $addTaxonomyToAgentSingle);

        // Registra o ícone do widget de Comunidades
        $app->hook('component(mc-icon).iconset', function(&$iconset) {
            $iconset['hand'] = 'ion:hand-right';
        });
    }
}

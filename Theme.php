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

        $app->hook('auth.successful', function () use ($app) {
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
    }
}

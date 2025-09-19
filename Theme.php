<?php
namespace SOM;

use MapasCulturais\App;


// class Theme extends \Subsite\Theme {
class Theme extends \MapasCulturais\Themes\BaseV2\Theme {

    static function getThemeFolder() {
        return __DIR__;
    }

    function _init() {
        parent::_init();

        $app = App::i();

        $this->assetManager->publishFolder('custom-fonts');
        $app->hook('mapasculturais.body:after', function() use ($app) {
            $this->part('theme-css');
        });
    }
}

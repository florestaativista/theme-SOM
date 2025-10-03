<?php

use MapasCulturais\i;

$this->import("
    home-entities
    home-logo-strip
    home-opportunities
");
?>

<div></div>

<div class="som-home">
    <div class="som-home__banner">
        <img src="<?php $this->asset('img/som-banner.jpg') ?>" alt="SOM">
        <div class="som-home__hero">
            <div class="som-home__hero-button">
                <a class="button button--primary button--large" href="<?= $app->createUrl('panel', 'index') ?>"><?php i::_e("Crie seu perfil agora") ?></a>
            </div>
            <p><?php i::_e("Uma plataforma desenvolvida para bandas e artistas independentes.") ?></p>
        </div>
    </div>

    <home-opportunities></home-opportunities>
    <home-entities></home-entities>
    <home-logo-strip></home-logo-strip>
</div>

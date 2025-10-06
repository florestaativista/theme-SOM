<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-link
');
?>
<div class="home-entities">
    <div class="home-entities__content">
        <div class="home-entities__content--header">
            <label class="title">
                <?= $this->text('title', i::__('Aqui você encontra as informações da cultura de sua região!')) ?>
            </label>
        </div>

        <div class="home-entities__content--cards">
            <mc-link route="search/opportunities">
                <div v-if="global.enabledEntities.opportunities" class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                            <img src="<?php $this->asset('img/home/home-entities/oportunidades.png') ?>" />
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Aqui você encontra as melhores oportunidades para apresentar sua música ao mundo, se conectar a diversos produtores, artistas, festivais, eventos e fazer parte do novo mapa da música. Clique e inscreva-se.') ?></p>
                        <mc-link route="search/opportunities" class="button button--icon button--sm opportunity__color">
                            <?= i::__('Ver todos')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>

            <mc-link route="search/artists">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                        <img src="<?php $this->asset('img/home/home-entities/artistas.png'); ?>" />
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Crie seu perfil e se conecte a festivais, produtores, a outros artistas e venha compor o novo mapa da música.') ?></p>
                        <mc-link route="search/artists" class="button button--icon button--sm agent__color">
                            <?= i::__('Ver todos')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>

            <mc-link route="search/producers">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                        <img src="<?php $this->asset('img/home/home-entities/producao.png'); ?>" />
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Traga sua produtora, seu festival, seu selo, a banda ou artistas que você produz e venha construir e se conectar à nova cena musical brasileira. ') ?></p>
                        <mc-link route="search/producers" class="button button--icon button--sm agent__color">
                            <?= i::__('Ver todos')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>
        </div>
    </div>
</div>

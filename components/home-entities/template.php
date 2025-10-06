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
            <div class="title">
                <?= i::__('Conecte-se com a cena da sua região') ?>
            </div>
            <div class="description">
                <?= i::__('O SOM é a plataforma para quem faz a música pulsar: produtores, artistas, festivais e casas de show. Participe de oportunidades, troque experiências e ajude a desenhar o novo mapa da música brasileira.') ?>
            </div>
        </div>

        <div class="home-entities__content--cards">
            <mc-link route="search/opportunities" v-if="global.enabledEntities.opportunities">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                            <img src="<?php $this->asset('img/home/home-entities/oportunidades.png') ?>" alt="<?= i::esc_attr__('Oportunidades') ?>"/>
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Aqui você encontra as melhores oportunidades para apresentar sua música ao mundo, se conectar a diversos produtores, artistas, festivais, eventos e fazer parte do novo mapa da música. Clique e inscreva-se.') ?></p>
                        <mc-link route="search/opportunities" class="button button--icon button--sm opportunity__color">
                            <?= i::_x('Ver todas', 'oportunidades')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>

            <mc-link route="search/artists">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                        <img src="<?php $this->asset('img/home/home-entities/artistas.png'); ?>" alt="<?= i::esc_attr__('Artistas') ?>" />
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Crie seu perfil e se conecte a festivais, produtores, a outros artistas e venha compor o novo mapa da música.') ?></p>
                        <mc-link route="search/artists" class="button button--icon button--sm agent__color">
                            <?= i::_x('Ver todos', 'artistas')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>

            <mc-link route="search/producers">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                        <img src="<?php $this->asset('img/home/home-entities/produtores.png'); ?>" alt="<?= i::esc_attr__('Produtores') ?>"/>
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Traga sua produtora, seu festival, seu selo, a banda ou artistas que você produz e venha construir e se conectar à nova cena musical brasileira.') ?></p>
                        <mc-link route="search/producers" class="button button--icon button--sm agent__color">
                            <?= i::_x('Ver todos', 'produtores')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>

            <mc-link route="search/spaces" v-if="global.enabledEntities.spaces">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                            <img src="<?php $this->asset('img/home/home-entities/casas.png') ?>" alt="<?= i::esc_attr__('Casas de show') ?>"/>
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('O nascedouro das cenas — os palcos onde tudo começa.') ?></p>
                        <p><?= i::__('Sem as casas de shows, não haveria renovação nem efervescência na música. Cadastre seu espaço e faça parte do nosso mapa.') ?></p>
                        <mc-link route="search/spaces" class="button button--icon button--sm space__color">
                            <?= i::_x('Ver todas', 'casas')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>

            <mc-link route="search/projects" v-if="global.enabledEntities.projects">
                <div class="card">
                    <div class="card__left">
                        <div class="card__left--img">
                            <img src="<?php $this->asset('img/home/home-entities/festivais.png') ?>" alt="<?= i::esc_attr__('Festivais') ?>"/>
                        </div>
                    </div>
                    <div class="card__right">
                        <p><?= i::__('Os festivais brasileiros se conectam aqui para fortalecer ações em rede, promover intercâmbios, construir parcerias e se aproximar de artistas e produtores. Se você é realizador de festival, cadastre seu projeto e faça parte dessa rede que impulsiona a música brasileira.') ?></p>
                        <mc-link route="search/projects" class="button button--icon button--sm project__color">
                            <?= i::_x('Ver todos', 'festivais')?>
                            <mc-icon name="access"></mc-icon>
                        </mc-link>
                    </div>
                </div>
            </mc-link>
        </div>
    </div>
</div>

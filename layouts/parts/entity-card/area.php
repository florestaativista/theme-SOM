<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use \MapasCulturais\i;
?>

<div v-if="entity.__objectType === 'agent' && (entity.terms.funcao_musica?.length)" class="entity-card__content--terms-area">
    <div class="area__title">
        <?php i::_e('Funções na música:') ?> ({{entity.terms.funcao_musica.length}}):
    </div>
    <p :class="['terms', 'agent__color']"> {{entity.terms.funcao_musica.join(", ")}} </p>
</div>

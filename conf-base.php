<?php
use MapasCulturais\i;

// Este arquivo é lido em App::_initTheme(), antes de Theme::_init()
// carregar o catálogo do tema (translations/{locale}.po) — i::__() aqui
// resolveria sempre contra o catálogo vazio do tema. Ver o mesmo padrão em
// themes/FlorestaAtivista/conf-base.php.
$lcode = i::get_locale();

$t = function (array $variants) use ($lcode) {
    return $variants[$lcode] ?? $variants['pt_BR'];
};

$_config = [
    'footer.supportMessage' => sprintf($t([
        'pt_BR' => 'Precisa de ajuda? Acesse o chat no rodapé da página ou envie um email para %s para falar com nossa equipe de suporte.',
        'es_ES' => '¿Necesita ayuda? Acceda al chat en el pie de página o envíe un correo a %s para hablar con nuestro equipo de soporte.',
        'en_US' => 'Need help? Access the chat in the page footer or send an email to %s to talk to our support team.',
    ]), '<a href="mail:suporte@som.vc">suporte@som.vc</a>'),
    'address.defaultCountryCode' => '',
    'app.defaultCountry' => '',
    'mailer.from' => 'plataformas@som.vc'
];

return $_config;
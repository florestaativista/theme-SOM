<?php
use MapasCulturais\i;

$_config = [
    'footer.supportMessage' => sprintf(i::__("Precisa de ajuda? Acesse o chat no rodapé da página ou envie um email para %s para falar com nossa equipe de suporte."), '<a href="mail:suporte@som.vc">suporte@som.vc</a>'),
    'address.defaultCountryCode' => '',
    'app.defaultCountry' => '',
    'mailer.from' => 'plataformas@som.vc'
];

return $_config;
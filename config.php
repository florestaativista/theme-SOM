<?php

use \MapasCulturais\i;

$this->config['routes']['shortcuts'] += [
    'artistas' => ['search', 'artists'],
    'produtores' => ['search', 'producers'],
    'casas' => ['search', 'spaces'],
    'festivais' => ['search', 'projects'],
];

unset(
    $this->config['routes']['shortcuts']['espacos'], 
    $this->config['routes']['shortcuts']['projetos']
);

$this->config['plugins']['Zammad'] = [
    'namespace' => 'Zammad',
    'config' => [
        'enabled' => true,
        'url' => 'https://suporte.florestaativista.org/assets/chat/chat-no-jquery.min.js',    
        'background' => '#c9a7ff',
        'title' => 'Duvidas? Fale conosco',
    ]
];

return [
    'app.enabled.events' => false,
    'app.enabled.spaces' => true,
    'app.enabled.projects' => true,

    'app.siteName' => 'SOM',
    'app.siteDescription' => 'O SOM é a plataforma para quem faz a música pulsar: produtores, artistas, festivais e casas de show. Participe de oportunidades, troque experiências e ajude a desenhar o novo mapa da música brasileira.',

    'logo.image' => 'img/logo-som.png',

    // i::__() aqui roda antes de Theme::_init() carregar o catálogo do
    // tema (mesmo motivo documentado em conf-base.php), por isso a
    // tradução é resolvida direto pelo locale.
    'text:home-opportunities.description' => [
        'pt_BR' => 'Cadastre-se e participe de oportunidades',
        'es_ES' => 'Regístrese y participe de oportunidades',
        'en_US' => 'Sign up and take part in opportunities',
    ][i::get_locale()] ?? 'Cadastre-se e participe de oportunidades',
];

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
    'enabled' => true,
    'url' => 'https://suporte.florestaativista.org/assets/chat/chat-no-jquery.min.js',    
    'background' => '#F66968',
    'title' => 'Duvidas? Fale conosco',
];
return [
    'app.enabled.events' => false,
    'app.enabled.spaces' => true,
    'app.enabled.projects' => true,

    'app.siteName' => 'SOM',

    'logo.image' => 'img/logo-som.png',

    'text:home-opportunities.description' => i::__('Cadastre-se e participe de oportunidades'),
];

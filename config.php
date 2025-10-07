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

return [
    'app.enabled.events' => false,
    'app.enabled.spaces' => true,
    'app.enabled.projects' => true,

    'app.siteName' => 'SOM',

    'logo.image' => 'img/logo-som.png',

    'text:home-opportunities.description' => i::__('Cadastre-se e participe de oportunidades'),
];

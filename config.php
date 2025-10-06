<?php

use \MapasCulturais\i;

$this->config['routes']['shortcuts'] += [
    'artistas' => ['search', 'artists'],
    'producao' => ['search', 'producers'],
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

    'text:home-entities.opportunities' => i::__('Aqui você encontra as melhores oportunidades para apresentar sua música ao mundo, se conectar a diversos produtores, artistas, festivais, eventos e fazer parte do novo mapa da música. Clique e inscreva-se.'),
    'text:home-opportunities.description' => i::__('Cadastre-se e participe de oportunidades'),
];

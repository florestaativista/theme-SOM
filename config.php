<?php

return [
    'app.enabled.events' => false,
    'app.enabled.spaces' => false,
    'app.enabled.projects' => false,

    'app.siteName' => 'SOM',

    'logo.image' => 'img/logo-som.png',


    'app.offline' => date('Y-m-d H:i:s') > '2025-09-19 09:00:00' && date('Y-m-d H:i:s') < '2025-10-03 08:00:00',
    'app.offlineUrl' => '/em-breve/',
    'app.offlineBypassFunction' => function() {
        $senha = $_GET['online'] ?? '';
        
        if ($senha === env('OFFLINE_BYPASS')) {
            $_SESSION['online'] = true;
        }

        return $_SESSION['online'] ?? false;
    }
];

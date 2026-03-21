<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    $path = dirname(__DIR__);
    if (file_exists($path.'/.env')) {
        (new Dotenv())->bootEnv($path.'/.env');
    } else {
        (new Dotenv())->bootEnv($path.'/.env.test');
    }
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

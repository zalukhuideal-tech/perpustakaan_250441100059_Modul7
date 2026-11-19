<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (getenv('VERCEL') && ! getenv('VIEW_COMPILED_PATH')) {
    putenv('VIEW_COMPILED_PATH=/tmp');
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp';
    $_SERVER['VIEW_COMPILED_PATH'] = '/tmp';
}

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->usePublicPath(__DIR__ . '/../public');

$app->handleRequest(Request::capture());
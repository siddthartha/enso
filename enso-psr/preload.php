<?php declare(strict_types = 1);

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotEnv = Dotenv::createImmutable(__DIR__);
$dotEnv->load();

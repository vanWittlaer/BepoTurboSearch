<?php declare(strict_types=1);

// Simple bootstrap for unit tests - no database required
$composerAutoloader = __DIR__ . '/../../../../vendor/autoload.php';

if (!file_exists($composerAutoloader)) {
    throw new RuntimeException('Could not find autoloader at ' . $composerAutoloader);
}

$loader = require $composerAutoloader;

// Register plugin namespace
$loader->addPsr4('Bepo\\TurboSuggest\\', __DIR__ . '/../src/');

// Register test namespace
$loader->addPsr4('Bepo\\TurboSuggest\\Tests\\', __DIR__);

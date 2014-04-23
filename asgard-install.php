<?php
require_once __DIR__.'/../utils/FileManager.php';

\Asgard\Utils\FileManager::copy(__DIR__.'/app/admin', 'app/admin');
\Asgard\Utils\FileManager::copy(__DIR__.'/tests/admin', 'tests/admin');
\Asgard\Utils\FileManager::copy(__DIR__.'/web/admin', 'web/admin');
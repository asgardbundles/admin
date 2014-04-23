<?php
require_once 'paths.php';
require_once _VENDOR_DIR_.'autoload.php'; #composer autoloader
\Asgard\Core\App::loadDefaultApp();

\Asgard\Utils\FileManager::copy(__DIR__.'/app/admin', _DIR_.'app/admin');
\Asgard\Utils\FileManager::copy(__DIR__.'/tests/admin', _DIR_.'tests/admin');
\Asgard\Utils\FileManager::copy(__DIR__.'/web/admin', _DIR_.'web/admin');

\Asgard\Utils\FileManager::copy(__DIR__.'/migrations/fixtures/admin.yml', _DIR_.'migrations/fixtures/admin.yml');
\Asgard\Orm\Libs\MigrationsManager::addMigrationFile(__DIR__.'/migrations/Admin.php');
\Asgard\Orm\Libs\MigrationsManager::migrate('Admin');

<?php
require_once 'paths.php';
require _CORE_DIR_.'core.php';
\Asgard\Core\App::loadDefaultApp();

\Asgard\Utils\FileManager::copy(__DIR__.'/app/admin', _DIR_.'app/admin');
\Asgard\Utils\FileManager::copy(__DIR__.'/tests/admin', _DIR_.'tests/admin');
\Asgard\Utils\FileManager::copy(__DIR__.'/web/admin', _DIR_.'web/admin');

\Asgard\Utils\FileManager::copy(__DIR__.'/migrations/fixtures/admin.yml', _DIR_.'migrations/fixtures/admin.yml');
\Asgard\Orm\MigrationsManager::addMigrationFile(__DIR__.'/migrations/Admin.php');
\Asgard\Orm\MigrationsManager::migrate('Admin');

<?php
function __($key, $params=array()) {
	return \Asgard\Container\Container::singleton()['translator']->trans($key, $params);
}

function d() {
	call_user_func_array(array('Asgard\Debug\Debug', 'dWithTrace'), array_merge([debug_backtrace()], func_get_args()));
}

require_once 'vendor/autoload.php';
foreach(spl_autoload_functions() as $function) {
	if(is_array($function) && $function[0] instanceof \Composer\Autoload\ClassLoader)
		$function[0]->setUseIncludePath(true);
}
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/app');

#Bundles
$kernel = new \Asgard\Core\Kernel(__DIR__);
$kernel->addBundles(array(
	new \Asgard\Core\Bundle,
	new \Asgard\Data\Bundle,
	new \Asgard\Imagecache\Bundle,
	new \Admin\Bundle
));
$container = $kernel->getContainer();
$container['cache'] = new \Asgard\Cache\NullCache();
$kernel->load();

#DB
$container['config']->set('database', [
	'driver' => 'sqlite',
	'database' => ':memory:',
]);

#Translator
$container['translator'] = new \Symfony\Component\Translation\Translator('en', new \Symfony\Component\Translation\MessageSelector());

#set the EntitiesManager static instance for activerecord-like entities (e.g. new Article or Article::find())
\Asgard\Entity\EntityManager::setInstance($container['entitiesManager']);

#Database
$container['schema']->dropAll();
$mm = new \Asgard\Migration\MigrationsManager(__DIR__.'/Migrations', $container);
$mm->migrateFile(__DIR__.'/Migrations/Admin.php');
$mm->migrateFile('vendor/asgard/data/Migrations/Data.php');
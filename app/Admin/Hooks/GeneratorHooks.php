<?php
namespace Admin\Hooks;

class GeneratorHooks extends \Asgard\Hook\HooksContainer {
	/**
	 * @Hook("Asgard.Core.Generate.bundleBuild")
	 */
	public static function bundleBuild(\Asgard\Hook\HookChain $chain, &$bundle, $dst, $generator) {
		foreach($bundle['entities'] as $name=>$entity) {
			if(!array_key_exists('admin', $entity))
				continue;
			if(!is_array($entity['admin']))
				$entity['admin'] = [];

			$entityClass = $entity['meta']['entityClass'];
			if(!isset($entity['admin']['form'])) {
				foreach($entityClass::properties() as $propname=>$prop) {
					if($prop->editable !== false)
						$entity['admin']['form'][$propname] = ['render'=>'def', 'params'=>[]];
				}
			}

			if(!isset($entity['admin']['relations']))
				$entity['admin']['relations'] = [];
			foreach($entity['admin']['relations'] as $relation)
				$entity['admin']['form'][] = $relation;
			foreach($entity['admin']['form'] as $property=>$params) {
				if(is_int($property)) {
					unset($entity['admin']['form'][$property]);
					$property = $params;
					$params = [];
					$entity['admin']['form'][$property] = $params;
				}
				if(!isset($params['render']))
					$entity['admin']['form'][$property]['render'] = 'def';
				if(!isset($params['params']))
					$entity['admin']['form'][$property]['params'] = [];
			}

			if(!isset($entity['admin']['messages']['modified']))
				$entity['admin']['messages']['modified'] = ucfirst($bundle['entities'][$name]['meta']['label']).' modified with success.';
			if(!isset($entity['admin']['messages']['created']))
				$entity['admin']['messages']['created'] = ucfirst($bundle['entities'][$name]['meta']['label']).' created with success.';
			if(!isset($entity['admin']['messages']['many_deleted']))
				$entity['admin']['messages']['many_deleted'] = ucfirst($bundle['entities'][$name]['meta']['label_plural']).' deleted with success.';
			if(!isset($entity['admin']['messages']['deleted']))
				$entity['admin']['messages']['deleted'] = ucfirst($bundle['entities'][$name]['meta']['label']).' deleted with success.';

			$generator->processFile(__DIR__.'/../generator/_EntityAdminController.php', $dst.'Controllers/'.ucfirst($bundle['entities'][$name]['meta']['name']).'AdminController.php', ['bundle'=>$bundle, 'entity'=>$entity]);
			$generator->processFile(__DIR__.'/../generator/html/index.php', $dst.'html/'.strtolower($bundle['entities'][$name]['meta']['name']).'admin/index.php', ['bundle'=>$bundle, 'entity'=>$entity]);
			$generator->processFile(__DIR__.'/../generator/html/form.php', $dst.'html/'.strtolower($bundle['entities'][$name]['meta']['name']).'admin/form.php', ['bundle'=>$bundle, 'entity'=>$entity]);

			$generator->processFile(__DIR__.'/../generator/web/ckeditor_config.js.php', $dst.'web/'.$bundle['entities'][$name]['meta']['name'].'/ckeditor_config.js', ['bundle'=>$bundle]);
			$generator->processFile(__DIR__.'/../generator/web/day_wysiwyg.css.php', $dst.'web/'.$bundle['entities'][$name]['meta']['name'].'/day_wysiwyg.css', ['bundle'=>$bundle]);

			if($bundle['tests']) {
				$class = '\\'.ucfirst($bundle['namespace']).'\\Controllers\\'.ucfirst($entity['meta']['name']).'AdminController';

				$indexRoute = $class::routeFor('index')->getRoute();
				$newRoute = $class::routeFor('new')->getRoute();
				$editRoute = $class::routeFor('edit')->getRoute();
				$deleteRoute = $class::routeFor('delete')->getRoute();
				$bundle['generatedTests'][$indexRoute] = '
		$browser = $this->getBrowser();
		$browser->setSession(\'admin_id\', 1);
		$this->assertTrue($browser->get(\''.$indexRoute.'\')->isOK(), \'GET '.$indexRoute.'\');
		$this->assertTrue($browser->get(\''.$newRoute.'\')->isOK(), \'GET '.$newRoute.'\');
		\\'.$entityClass.'::create([\'id\'=>50, ]);
		$this->assertTrue($browser->get(\''.str_replace(':id', 50, $editRoute).'\')->isOK(), \'GET '.$editRoute.'\');
		$this->assertTrue($browser->get(\''.str_replace(':id', 50, $deleteRoute).'\')->isOK(), \'GET '.$deleteRoute.'\');';
			}
		}
	}

	/**
	 * @Hook("Asgard.Core.Generate.bundlephp")
	 */
	public static function bundle(\Asgard\Hook\HookChain $chain, $bundle) {
		foreach($bundle['entities'] as $name=>$entity) {
			echo "
		\$container['hooks']->hook('Asgard.Http.Start', function(\$chain, \$request) {
				\$chain->container['adminMenu']->add([
				'label' => __('".ucfirst($entity['meta']['label_plural'])."'),
				'link' => \$chain->container['resolver']->url_for(['".$bundle['namespace']."\Controllers\\".ucfirst($entity['meta']['name'])."AdminController', 'index']),
			], '0.');
			\$chain->container['adminMenu']->addHome([
				'img' => \$chain->container['request']->url->to('".$bundle['name']."/".$entity['meta']['plural'].".svg'),
				'link' => \$chain->container['resolver']->url_for(['".$bundle['namespace']."\Controllers\\".ucfirst($entity['meta']['name']),"AdminController', 'index']),
				'title' => __('".ucfirst($entity['meta']['label_plural'])."'),
				'description' => __('')
			]);
		});

";
		}
	}
}
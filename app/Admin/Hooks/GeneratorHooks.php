<?php
namespace Admin\Hooks;

class GeneratorHooks extends \Asgard\Hook\HookContainer {
	/**
	 * @Hook("Asgard.Core.Generate.postBundleBuild")
	 */
	public static function bundleBuild(\Asgard\Hook\Chain $chain, &$bundle, $dst, $generator) {
		if(!isset($bundle['admin']['entities']))
			return;

		foreach($bundle['admin']['entities'] as $name=>$entity) {
			$meta = $entity['meta'] = static::getMeta($bundle, $name);

			$entityClass = $meta['entityClass'];
			if(!isset($entity['form'])) {
				foreach($entityClass::properties() as $propname=>$prop) {
					if($prop->editable !== false)
						$entity['form'][$propname] = ['render'=>'def', 'params'=>[]];
				}
			}

			if(!isset($entity['relations']))
				$entity['relations'] = [];
			foreach($entity['relations'] as $relation)
				$entity['form'][] = $relation;
			foreach($entity['form'] as $property=>$params) {
				if(is_int($property)) {
					unset($entity['form'][$property]);
					$property = $params;
					$params = [];
					$entity['form'][$property] = $params;
				}
				if(!isset($params['render']))
					$entity['form'][$property]['render'] = 'def';
				if(!isset($params['params']))
					$entity['form'][$property]['params'] = [];
			}

			if(!isset($entity['messages']['modified']))
				$entity['messages']['modified'] = ucfirst($meta['label']).' modified with success.';
			if(!isset($entity['messages']['created']))
				$entity['messages']['created'] = ucfirst($meta['label']).' created with success.';
			if(!isset($entity['messages']['many_deleted']))
				$entity['messages']['many_deleted'] = ucfirst($meta['label_plural']).' deleted with success.';
			if(!isset($entity['messages']['deleted']))
				$entity['messages']['deleted'] = ucfirst($meta['label']).' deleted with success.';

			$generator->processFile(__DIR__.'/../generator/_EntityAdminController.php', $dst.'Controllers/'.ucfirst($meta['name']).'AdminController.php', ['bundle'=>$bundle, 'entity'=>$entity]);
			$generator->processFile(__DIR__.'/../generator/html/index.php', $dst.'html/'.strtolower($meta['name']).'admin/index.php', ['bundle'=>$bundle, 'entity'=>$entity]);
			$generator->processFile(__DIR__.'/../generator/html/form.php', $dst.'html/'.strtolower($meta['name']).'admin/form.php', ['bundle'=>$bundle, 'entity'=>$entity]);

			$generator->processFile(__DIR__.'/../generator/web/ckeditor_config.js.php', $dst.'web/'.$meta['name'].'/ckeditor_config.js', ['bundle'=>$bundle]);
			$generator->processFile(__DIR__.'/../generator/web/day_wysiwyg.css.php', $dst.'web/'.$meta['name'].'/day_wysiwyg.css', ['bundle'=>$bundle]);

			if($bundle['tests']) {
				$class = '\\'.ucfirst($bundle['namespace']).'\\Controllers\\'.ucfirst($meta['name']).'AdminController';

				$indexRoute = $class::routeFor('index')->getRoute();
				$newRoute = $class::routeFor('new')->getRoute();
				$editRoute = $class::routeFor('edit')->getRoute();
				$deleteRoute = $class::routeFor('delete')->getRoute();
				$bundle['generatedTests'][$indexRoute] = '
		$browser = $this->getBrowser();
		$browser->getSession()->set(\'admin_id\', 1);
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
	public static function bundle(\Asgard\Hook\Chain $chain, $bundle) {
		if(!isset($bundle['admin']['entities']))
			return;

		foreach($bundle['admin']['entities'] as $name=>$entity) {
			$bundleName = $bundle['name'];
			$namespace = $bundle['namespace'];
			$meta = static::getMeta($bundle, $name);
			$labelPlural = ucfirst($meta['label_plural']);
			$entityName = ucfirst($meta['name']);
			$entityPlural = $meta['plural'];

			echo "
		\$container['hooks']->hook('Asgard.Http.Start', function(\$chain, \$request) {
				\$chain->getContainer()['adminMenu']->add([
				'label' => __('".$labelPlural."'),
				'link' => \$chain->getContainer()['resolver']->url(['".$namespace."\Controllers\\".$entityName."AdminController', 'index']),
			], '0.');
			\$chain->getContainer()['adminMenu']->addHome([
				'img' => \$chain->getContainer()['httpKernel']->getRequest()->url->to('bundles/".$bundleName."/".$entityPlural.".svg'),
				'link' => \$chain->getContainer()['resolver']->url(['".$namespace."\Controllers\\".$entityName,"AdminController', 'index']),
				'title' => __('".$labelPlural."'),
				'description' => __('')
			]);
		});

		\$container['adminManager']->setAlias('".$meta['plural']."', '".$namespace."\Entities\\".$entityName."');
";
		}
	}

	protected static function getMeta($bundle, $name) {
		if(isset($meta))
			return $meta;

		if(!isset($bundle['admin']['entities'][$name]['meta']))
			$bundle['admin']['entities'][$name]['meta'] = [];
		if(isset($bundle['admin']['entities'][$name]['meta']['name']))
			$bundle['admin']['entities'][$name]['meta']['name'] = strtolower($bundle['admin']['entities'][$name]['meta']['name']);
		else
			$bundle['admin']['entities'][$name]['meta']['name'] = strtolower($name);

		if(!isset($bundle['admin']['entities'][$name]['meta']['entityClass']))
			$bundle['admin']['entities'][$name]['meta']['entityClass'] = $bundle['namespace'].'\Entities\\'.ucfirst($name);

		if(isset($bundle['admin']['entities'][$name]['meta']['plural']))
			$bundle['admin']['entities'][$name]['meta']['plural'] = strtolower($bundle['admin']['entities'][$name]['meta']['plural']);
		else
			$bundle['admin']['entities'][$name]['meta']['plural'] = $bundle['admin']['entities'][$name]['meta']['name'].'s';
		if(isset($bundle['admin']['entities'][$name]['meta']['label']))
			$bundle['admin']['entities'][$name]['meta']['label'] = strtolower($bundle['admin']['entities'][$name]['meta']['label']);
		else
			$bundle['admin']['entities'][$name]['meta']['label'] = $bundle['admin']['entities'][$name]['meta']['name'];
		if(isset($bundle['admin']['entities'][$name]['meta']['label_plural']))
			$bundle['admin']['entities'][$name]['meta']['label_plural'] = strtolower($bundle['admin']['entities'][$name]['meta']['label_plural']);
		elseif(isset($bundle['admin']['entities'][$name]['meta']['plural']))
			$bundle['admin']['entities'][$name]['meta']['label_plural'] = strtolower($bundle['admin']['entities'][$name]['meta']['plural']);
		else
			$bundle['admin']['entities'][$name]['meta']['label_plural'] = $bundle['admin']['entities'][$name]['meta']['label'].'s';
		if(!isset($bundle['admin']['entities'][$name]['meta']['name_field'])) {
			$properties = array_keys($bundle['entities'][$name]['properties']);
			$bundle['admin']['entities'][$name]['meta']['name_field'] = $properties[0];
		}

		return $bundle['admin']['entities'][$name]['meta'];
	}
}
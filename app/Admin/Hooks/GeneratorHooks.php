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
					if($prop->get('editable') !== false && $prop->get('type') !== 'entity')
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
				$routes = $chain->getContainer()['controllersAnnotationReader']->fetchRoutes($class);
				$chain->getContainer()['resolver']->addRoutes($routes);

				$indexRoute = $chain->getContainer()['resolver']->getRouteFor([$class, 'index'])->getRoute();
				$newRoute = $chain->getContainer()['resolver']->getRouteFor([$class, 'new'])->getRoute();
				$editRoute = $chain->getContainer()['resolver']->getRouteFor([$class, 'edit'])->getRoute();
				$deleteRoute = $chain->getContainer()['resolver']->getRouteFor([$class, 'delete'])->getRoute();
				$bundle['generatedTests'][] = '
		$browser = $this->createBrowser();
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
		if(isset($bundle['entities'][$name]['meta']))
			$meta = $bundle['entities'][$name]['meta'];
		elseif(isset($bundle['admin']['entities'][$name]['meta']))
			$meta = $bundle['admin']['entities'][$name]['meta'];
		else
			$meta = [];

		if(isset($meta['name']))
			$meta['name'] = strtolower($meta['name']);
		else
			$meta['name'] = strtolower($name);

		if(!isset($meta['entityClass']))
			$meta['entityClass'] = $bundle['namespace'].'\Entities\\'.ucfirst($name);

		if(isset($meta['plural']))
			$meta['plural'] = strtolower($meta['plural']);
		else
			$meta['plural'] = $meta['name'].'s';
		if(isset($meta['label']))
			$meta['label'] = strtolower($meta['label']);
		else
			$meta['label'] = $meta['name'];
		if(isset($meta['label_plural']))
			$meta['label_plural'] = strtolower($meta['label_plural']);
		elseif(isset($meta['plural']))
			$meta['label_plural'] = strtolower($meta['plural']);
		else
			$meta['label_plural'] = $meta['label'].'s';
		if(!isset($meta['name_field'])) {
			$properties = array_keys($bundle['entities'][$name]['properties']);
			$meta['name_field'] = $properties[0];
		}

		return $meta;
	}
}
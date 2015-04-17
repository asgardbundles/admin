<?php
namespace Admin;

class AdminGenerator extends \Asgard\Generator\AbstractGenerator {
	protected $resolver;
	protected $translator;
	protected $testBuilder;

	public function __construct(\Asgard\Http\ResolverInterface $resolver, \Symfony\Component\Translation\TranslatorInterface $translator, \Asgard\Tester\TestBuilderInterface $testBuilder) {
		$this->resolver = $resolver;
		$this->translator = $translator;
		$this->testBuilder = $testBuilder;
	}

	public function preGenerate(array &$bundle) {
		foreach($bundle['admin']['entities'] as $entityName=>&$entity)
			$entity['meta'] = $this->getMeta($bundle, $entityName);
	}

	public function postGenerate(array $bundle, $root, $bundlePath) {
		if(!isset($bundle['admin']['entities']))
			return;

		$tests = [];
		foreach($bundle['admin']['entities'] as $entityName=>$entity) {
			$meta = $entity['meta'];

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
				$entity['messages']['modified'] = $this->translator->trans('admin.generator.modified', [':label'=>ucfirst($meta['label'])]);
			if(!isset($entity['messages']['created']))
				$entity['messages']['created'] = $this->translator->trans('admin.generator.created', [':label'=>ucfirst($meta['label'])]);
			if(!isset($entity['messages']['many_deleted']))
				$entity['messages']['many_deleted'] = $this->translator->trans('admin.generator.deleted', [':label'=>ucfirst($meta['label_plural'])]);
			if(!isset($entity['messages']['deleted']))
				$entity['messages']['deleted'] = $this->translator->trans('admin.generator.deletedPlural', [':label'=>ucfirst($meta['label'])]);

			$this->engine->processFile(__DIR__.'/generator/_EntityAdminController.php', $bundlePath.'Controllers/'.ucfirst($meta['name']).'AdminController.php', ['bundle'=>$bundle, 'entity'=>$entity]);
			$this->engine->processFile(__DIR__.'/generator/html/index.php', $bundlePath.'html/'.strtolower($meta['name']).'admin/index.php', ['bundle'=>$bundle, 'entity'=>$entity]);
			$this->engine->processFile(__DIR__.'/generator/html/form.php', $bundlePath.'html/'.strtolower($meta['name']).'admin/form.php', ['bundle'=>$bundle, 'entity'=>$entity]);

			if(isset($meta['wysiwyg']) && $meta['wysiwyg']) {
				$this->engine->processFile(__DIR__.'/generator/web/ckeditor_config.js.php', $root.'web/'.$meta['name'].'/ckeditor_config.js', ['bundle'=>$bundle]);
				$this->engine->processFile(__DIR__.'/generator/web/day_wysiwyg.css.php', $root.'web/'.$meta['name'].'/day_wysiwyg.css', ['bundle'=>$bundle]);
			}

			if(isset($bundle['test']) && $bundle['test']) {
				$class = '\\'.ucfirst($bundle['namespace']).'\\Controllers\\'.ucfirst($meta['name']).'AdminController';
				$routes = $chain->getContainer()['controllersAnnotationReader']->fetchRoutes($class);
				$this->resolver->addRoutes($routes);

				$indexRoute = $this->resolver->getRouteFor([$class, 'index']);
				$indexRouteStr = $indexRoute->getRoute();
				$newRoute = $this->resolver->getRouteFor([$class, 'new']);
				$newRouteStr = $newRoute->getRoute();
				$editRoute = $this->resolver->getRouteFor([$class, 'edit']);
				$editRouteStr = $editRoute->getRoute();
				$deleteRoute = $this->resolver->getRouteFor([$class, 'delete']);
				$deleteRouteStr = $deleteRoute->getRoute();
				$tests[] = [
					'test' => '
	public function testAdmin'.ucfirst($entityName).'() {
		$browser = $this->createBrowser();
		$browser->getSession()->set(\'admin_id\', 1);
		$this->assertTrue($browser->get(\''.$indexRouteStr.'\')->isOK(), \'GET '.$indexRouteStr.'\');
		$this->assertTrue($browser->get(\''.$newRouteStr.'\')->isOK(), \'GET '.$newRouteStr.'\');
		\\'.$entityClass.'::create([\'id\'=>50, ]);
		$this->assertTrue($browser->get(\''.str_replace(':id', 50, $editRouteStr).'\')->isOK(), \'GET '.$editRouteStr.'\');
		$this->assertTrue($browser->get(\''.str_replace(':id', 50, $deleteRouteStr).'\')->isOK(), \'GET '.$deleteRouteStr.'\');
	}',
					'routes' => [
						$indexRoute,
						$newRoute,
						$editRoute,
						$deleteRoute,
					]
				];
			}
		}

		$this->testBuilder->buildTests($tests, $bundle['name']);
	}

// 	public function postGenerate(array $bundle, $root, $bundlePath) {
// 		if(!isset($bundle['admin']['entities']))
// 			return;

// 		if(!isset($bundle['tests']) || $bundle['tests'] === false)
// 			return;

// 		$tests = [];
// 		foreach($bundle['admin']['entities'] as $entityName=>$entity) {
// 			$class = '\\'.ucfirst($bundle['namespace']).'\\Controllers\\'.ucfirst($meta['name']).'AdminController';
// 			$routes = $chain->getContainer()['controllersAnnotationReader']->fetchRoutes($class);
// 			$this->resolver->addRoutes($routes);

// 			$indexRoute = $this->resolver->getRouteFor([$class, 'index']);
// 			$indexRouteStr = $indexRoute->getRoute();
// 			$newRoute = $this->resolver->getRouteFor([$class, 'new']);
// 			$newRouteStr = $newRoute->getRoute();
// 			$editRoute = $this->resolver->getRouteFor([$class, 'edit']);
// 			$editRouteStr = $editRoute->getRoute();
// 			$deleteRoute = $this->resolver->getRouteFor([$class, 'delete']);
// 			$deleteRouteStr = $deleteRoute->getRoute();
// 			$tests[] = [
// 				'test' => '
// public function testAdmin'.ucfirst($entityName).'() {
// 	$browser = $this->createBrowser();
// 	$browser->getSession()->set(\'admin_id\', 1);
// 	$this->assertTrue($browser->get(\''.$indexRouteStr.'\')->isOK(), \'GET '.$indexRouteStr.'\');
// 	$this->assertTrue($browser->get(\''.$newRouteStr.'\')->isOK(), \'GET '.$newRouteStr.'\');
// 	\\'.$entityClass.'::create([\'id\'=>50, ]);
// 	$this->assertTrue($browser->get(\''.str_replace(':id', 50, $editRouteStr).'\')->isOK(), \'GET '.$editRouteStr.'\');
// 	$this->assertTrue($browser->get(\''.str_replace(':id', 50, $deleteRouteStr).'\')->isOK(), \'GET '.$deleteRouteStr.'\');
// }',
// 				'routes' => [
// 					$indexRoute,
// 					$newRoute,
// 					$editRoute,
// 					$deleteRoute,
// 				]
// 			];
// 		}
// 	}

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

		return $meta;
	}
}
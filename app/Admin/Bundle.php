<?php
namespace Admin;

class Bundle extends \Asgard\Core\BundleLoader {
	public function buildContainer(\Asgard\Container\ContainerInterface $container) {
		$container->register('adminMenu', function() {
			return new \Admin\Libs\AdminMenu;
		});
		$container->register('adminManager', function() {
			return new \Admin\Libs\AdminManager;
		});
		$container->register('adminAuth', function($container) {
			return new \Admin\Libs\AdminAuth($container);
		});
		$container->register('adminEntityFieldSolver', function() {
			$solver = new \Asgard\Entityform\EntityFieldSolver;
			$solver->addMany(function($property) {
				return new \Admin\Libs\Form\DynamicGroup;
			});
			$solver->addMany(function($property) {
				if($property instanceof \Asgard\Entity\Properties\FileProperty)
					return new \Admin\Libs\Form\Fields\MultipleFilesField;
			});
			return $solver;
		});
		$container->register('adminEntityForm', function($container, $entity, $controller, $params=[]) {
			$widgetManager = clone $container['widgetManager'];
			$EntityFieldSolver = new \Asgard\Entityform\EntityFieldSolver([$container['EntityFieldSolver'], $container['adminEntityFieldSolver']]);
			$form = new \Admin\Libs\Form\AdminEntityForm($entity, $controller, $params, $widgetManager, $EntityFieldSolver, $container['dataMapper']);
			$form->setValidatorFactory($container['validator_factory']);
			$form->setTranslator($container['translator']);
			return $form;
		});
		$container->register('adminSimpleForm', function($container, $controller, $name=null, $params=[]) {
			$widgetManager = clone $container['widgetManager'];
			$form = new \Admin\Libs\Form\AdminSimpleForm($controller, $name, $params, $widgetManager);
			$form->setTranslator($container['translator']);
			return $form;
		});
	}

	public function run(\Asgard\Container\ContainerInterface $container) {
		parent::run($container);

		if($container->has('console'))
			$container['generator']->addGenerator(new AdminGenerator($container['resolver'], $container['translator'], $container['controllersAnnotationReader'], $container['testBuilder']));

		$container['hooks']->hook('Asgard.Http.Start', function($chain, $request) {
			$chain->getContainer()['adminMenu']->add([
				'label'  => __('Configuration'),
				'link'   => '#',
				'childs' => [
					['label' => __('Preferences'), 'link' => $chain->getContainer()['resolver']->url(['Admin\Controller\PreferencesAdmin', 'edit'])],
					['label' => __('Administrators'), 'link' => $chain->getContainer()['resolver']->url(['Admin\Controller\AdministratorAdmin', 'index'])],
				]
			], 10);

			$chain->getContainer()['imagecache']->addPreset('admin_thumb', [
				'resize'	=>	[
					'height' =>	100,
					'force'  =>	false
				]
			]);
		});
	}
}
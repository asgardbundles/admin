<?php
namespace Admin;

class Bundle extends \Asgard\Core\BundleLoader {
	public function buildApp($app) {
		$app->register('adminMenu', function() {
			return new \Admin\Libs\AdminMenu();
		});
		$app->register('adminManager', function() {
			return new \Admin\Libs\AdminManager();
		});
		$app->register('adminAuth', function($app) {
			return new \Admin\Libs\AdminAuth($app);
		});
		$app->register('adminEntityFieldsSolver', function() {
			$solver = new \Asgard\Form\EntityFieldsSolver;
			return $solver;
		});
		$app->register('adminEntityForm', function($app, $entity, $controller, $params=[]) {
			$widgetsManager = clone $app['widgetsManager'];
			$entityFieldsSolver = new \Asgard\Form\EntityFieldsSolver([$app['entityFieldsSolver'], $app['adminEntityFieldsSolver']]);
			$form = new \Admin\Libs\Form\AdminEntityForm($entity, $controller, $params, $widgetsManager, $entityFieldsSolver);
			$form->setTranslator($app['translator']);
			$form->setApp($app);
			return $form;
		});
		$app->register('adminSimpleForm', function($app, $controller, $name=null, $params=[]) {
			$widgetsManager = clone $app['widgetsManager'];
			$form = new \Admin\Libs\Form\AdminSimpleForm($controller, $name, $params, $widgetsManager);
			$form->setTranslator($app['translator']);
			$form->setApp($app);
			return $form;
		});
	}

	public function run($app) {
		parent::run($app);

		$app['hooks']->hook('Asgard.Http.Start', function($chain, $request) {
			$chain->app['adminMenu']->add([
				'label'  => __('Configuration'),
				'link'   => '#',
				'childs' => [
					['label' => __('Preferences'), 'link' => $chain->app['resolver']->url_for(['Admin\Controllers\PreferencesAdminController', 'edit'])],
					['label' => __('Administrators'), 'link' => $chain->app['resolver']->url_for(['Admin\Controllers\AdministratorAdminController', 'index'])],
				]
			], 10);

			$chain->app['imagecache']->addPreset('admin_thumb', [
				'resize'	=>	[
					'height' =>	100,
					'force'  =>	false
				]
			]);
		});
	}
}
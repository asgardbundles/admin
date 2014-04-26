<?php
namespace App\Admin\Hooks;

class GeneratorHooks extends \Asgard\Hook\HooksContainer {
	/**
	@Hook('Agard\CLI\generator\bundleBuild')
	**/
	public static function bundleBuild($chain, &$bundle, $dst) {
		foreach($bundle['entities'] as $name=>$entity) {
			if(!isset($entity['admin']))
				continue;
			if(!is_array($entity['admin']))
				continue;

			$entityClass = $entity['meta']['entityClass'];
			if(!isset($entity['admin']['form']))
				$entity['admin']['form'] = array_keys($entityClass::properties());

			if(array_values($entity['admin']['form']) === $entity['admin']['form']) {
				$old = $entity['admin']['form'];
				$entity['admin']['form'] = array();
				foreach($old as $k=>$v)
					$entity['admin']['form'][$v] = array('render'=>'def', 'params'=>array());
			}
			foreach($entity['admin']['form'] as $property=>$params) {
				if(!$entityClass::hasProperty($property) || $entityClass::property($property)->editable === false) {
					unset($entity['admin']['form'][$property]);
					continue;
				}
				if(!isset($params['render']))
					$entity['admin']['form'][$property]['render'] = 'def';
				if(!isset($params['params']))
					$entity['admin']['form'][$property]['params'] = array();
			}

			if(!isset($entity['admin']['messages']['modified']))
				$entity['admin']['messages']['modified'] = ucfirst($bundle['entities'][$name]['meta']['label']).' modified with success.';
			if(!isset($entity['admin']['messages']['created']))
				$entity['admin']['messages']['created'] = ucfirst($bundle['entities'][$name]['meta']['label']).' created with success.';
			if(!isset($entity['admin']['messages']['many_deleted']))
				$entity['admin']['messages']['many_deleted'] = ucfirst($bundle['entities'][$name]['meta']['label_plural']).' deleted with success.';
			if(!isset($entity['admin']['messages']['deleted']))
				$entity['admin']['messages']['deleted'] = ucfirst($bundle['entities'][$name]['meta']['label']).' deleted with success.';

			\Asgard\Core\Cli\AsgardController::processFile(__DIR__.'/../generator/_EntityAdminController.php', $dst.'controllers/'.ucfirst($bundle['entities'][$name]['meta']['name']).'AdminController.php', array('bundle'=>$bundle, 'entity'=>$entity));
			\Asgard\Core\Cli\AsgardController::processFile(__DIR__.'/../generator/views/index.php', $dst.'views/'.$bundle['entities'][$name]['meta']['name'].'admin/index.php', array('bundle'=>$bundle, 'entity'=>$entity));
			\Asgard\Core\Cli\AsgardController::processFile(__DIR__.'/../generator/views/form.php', $dst.'views/'.$bundle['entities'][$name]['meta']['name'].'admin/form.php', array('bundle'=>$bundle, 'entity'=>$entity));

			\Asgard\Core\Cli\AsgardController::processFile(__DIR__.'/../generator/web/ckeditor_config.js.php', $dst.'web/'.$bundle['entities'][$name]['meta']['name'].'/ckeditor_config.js', array('bundle'=>$bundle));
			\Asgard\Core\Cli\AsgardController::processFile(__DIR__.'/../generator/web/day_wysiwyg.css.php', $dst.'web/'.$bundle['entities'][$name]['meta']['name'].'/day_wysiwyg.css', array('bundle'=>$bundle));

			if($bundle['tests']) {
				$class = '\\'.ucfirst($bundle['namespace']).'\\Controllers\\'.ucfirst($entity['meta']['name']).'AdminController';

				$indexRoute = $class::route_for('index');
				$newRoute = $class::route_for('new');
				$editRoute = $class::route_for('edit');
				$deleteRoute = $class::route_for('delete');
				$bundle['generatedTests'][$indexRoute] = '
		$browser = $this->getBrowser();
		$browser->setSession(\'admin_id\', 1);
		$this->assertTrue($browser->get(\''.$indexRoute.'\')->isOK(), \'GET '.$indexRoute.'\');
		$this->assertTrue($browser->get(\''.$newRoute.'\')->isOK(), \'GET '.$newRoute.'\');
		\\'.$entityClass.'::create(array(\'id\'=>50, ));
		$this->assertTrue($browser->get(\''.str_replace(':id', 50, $editRoute).'\')->isOK(), \'GET '.$editRoute.'\');
		$this->assertTrue($browser->get(\''.str_replace(':id', 50, $deleteRoute).'\')->isOK(), \'GET '.$deleteRoute.'\');';
			}
		}
	}

	/**
	@Hook('Agard\CLI\generator\bundle.php')
	**/
	public static function bundle($chain, $bundle) {
		foreach($bundle['entities'] as $name=>$entity) {
			echo "
			\App\Admin\Libs\AdminMenu::instance()->menu[0]['childs'][] = array('label' => '".ucfirst($entity['meta']['label_plural'])."', 'link' => '".$entity['meta']['plural']."'); 
			\App\Admin\Libs\AdminMenu::instance()->home[] = array('img'=>\Asgard\Core\App::get('url')->to('".$bundle['name']."/".$entity['meta']['plural'].".svg'), 'link'=>'".$entity['meta']['plural']."', 'title' => __('".ucfirst($entity['meta']['label_plural'])."'), 'description' => __('')); ";
		}
	}
}
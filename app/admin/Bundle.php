<?php
namespace App\Admin;

class Bundle extends \Asgard\Core\BundleLoader {
	public function load($queue) {
		$queue->addBundles(array(
			new \App\Imagecache\Bundle,
			new \Asgard\Orm\Bundle,
			new \Asgard\Data\Bundle,
		));

		parent::load($queue);
	}

	public function run() {
		\App\Admin\Libs\AdminMenu::instance()->menu[8] = array('label' => 'Configuration', 'link' => '#', 'childs' => array(
			array('label' => 'Preferences', 'link' => 'preferences'),
			array('label' => __('Administrators'), 'link' => 'administrators'),
		));

		\App\Imagecache\Libs\ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));

		\Asgard\Core\App::get('hook')->hook('start', function() {
			if(preg_match('/^admin/', \Asgard\Core\App::get('url')->get())) {
				\Asgard\Core\App::get('hook')->hookBefore('exception_Asgard\Core\Exceptions\NotFoundException', function() {
					return \Asgard\Core\Controller::run('\App\Admin\Controllers\DefaultAdminController', '_404', \Asgard\Core\App::get('request'))->setCode(404);
				});
			}
		});

		parent::run();
	}
}
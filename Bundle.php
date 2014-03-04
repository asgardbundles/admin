<?php
namespace Coxis\Admin;

class Bundle extends \Coxis\Core\BundleLoader {
	public function run() {
		\Coxis\Admin\Libs\AdminMenu::instance()->menu[8] = array('label' => 'Configuration', 'link' => '#', 'childs' => array(
			array('label' => 'Preferences', 'link' => 'preferences'),
			array('label' => __('Administrators'), 'link' => 'administrators'),
		));
		\Coxis\Imagecache\Libs\ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
		parent::run();
	}
}
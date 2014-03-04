<?php
namespace Asgard\Admin;

class Bundle extends \Asgard\Core\BundleLoader {
	public function run() {
		\Asgard\Admin\Libs\AdminMenu::instance()->menu[8] = array('label' => 'Configuration', 'link' => '#', 'childs' => array(
			array('label' => 'Preferences', 'link' => 'preferences'),
			array('label' => __('Administrators'), 'link' => 'administrators'),
		));
		\Asgard\Imagecache\Libs\ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
		parent::run();
	}
}
<?php
namespace App\Admin;

class Bundle extends \Coxis\Core\BundleLoader {
	public function run() {
		\App\Imagecache\Libs\ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
		parent::run();
	}
}
return new Bundle;
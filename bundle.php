<?php
namespace Coxis\Admin;

class Bundle extends \Coxis\Core\BundleLoader {
	public function run() {
		\Coxis\Imagecache\Libs\ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
		parent::run();
	}
}
return new Bundle;
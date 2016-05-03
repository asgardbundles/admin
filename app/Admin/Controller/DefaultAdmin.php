<?php
namespace Admin\Controller;

class DefaultAdmin extends \Admin\Libs\Controller\AdminParentController {
	/**
	 * @Route("admin")
	 */
	public function indexAction(\Asgard\Http\Request $request) {
	}

	public function _404Action() {
	}
}
<?php
namespace Admin\Controllers;

class DefaultAdminController extends \Admin\Libs\Controller\AdminParentController {
	/**
	 * @Route("admin")
	 */
	public function indexAction(\Asgard\Http\Request $request) {
	}

	public function _404Action() {
	}
}
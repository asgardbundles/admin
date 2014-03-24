<?php
namespace Asgard\Admin\Controllers;

class DefaultAdminController extends \Asgard\Admin\Libs\Controller\AdminParentController {
	public function configure() {
		return parent::configure();
	}
	
	/**
	@Route('admin')
	*/
	public function indexAction($request) {
	}
}
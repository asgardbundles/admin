<?php
namespace App\Admin\Controllers;

class DefaultAdminController extends \App\Admin\Libs\Controller\AdminParentController {
	public function configure($request) {
		return parent::configure($request);
	}
	
	/**
	@Route('admin')
	*/
	public function indexAction($request) {
	}
}
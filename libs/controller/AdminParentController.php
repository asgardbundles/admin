<?php
namespace Coxis\Admin\Libs\Controller;

abstract class AdminParentController extends \Coxis\Core\Controller {
	public function configure() {
		// Config::set('locale', 'en');
		$this->layout = array('\Coxis\Admin\Controllers\AdminController', 'layout');
		$this->htmlLayout = false;
		if(!\Coxis\Core\App::get('session')->get('admin_id')) {
			\Coxis\Core\App::get('session')->set('redirect_to', \Coxis\Core\App::get('url')->full());
			return \Coxis\Core\App::get('response')->setCode(401)->redirect('admin/login', true);
		}
	}
}
<?php
namespace App\Admin\Libs\Controller;

abstract class AdminParentController extends \App\Core\Controller {
	public function configure() {
		// Config::set('locale', 'en');
		$this->layout = array('\App\Admin\Controllers\AdminController', 'layout');
		$this->htmlLayout = false;
		if(!\Session::get('admin_id')) {
			\Session::set('redirect_to', \URL::full());
			return \Response::setCode(401)->redirect('admin/login', true);
		}
	}
}
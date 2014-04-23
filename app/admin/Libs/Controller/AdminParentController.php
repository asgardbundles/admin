<?php
namespace App\Admin\Libs\Controller;

abstract class AdminParentController extends \Asgard\Core\Controller {
	public function configure() {
		$this->layout = array('\App\Admin\Controllers\AdminController', 'layout');
		$this->htmlLayout = false;
		if(!$this->request->session->get('admin_id')) {
			$this->request->session->set('redirect_to', $this->request->url->full());
			return \Asgard\Core\App::get('response')->setCode(401)->redirect('admin/login', true);
		}
	}
}
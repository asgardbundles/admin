<?php
namespace Coxis\Admin\Controllers;

class LoginController extends \Coxis\Core\Controller {
	public function configure() {
		$this->layout = false;
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if(\Coxis\Core\App::get('session')->get('admin_id'))
			return $this->response->redirect('admin');
	
		$administrator = null;
		if(\Coxis\Core\App::get('post')->has('username'))
			$administrator = \Coxis\Admin\Entities\Administrator::where(array('username' => \Coxis\Core\App::get('post')->get('username'), 'password' => sha1(\Coxis\Core\App::get('config')->get('salt').\Coxis\Core\App::get('post')->get('password'))))->first();
		elseif(\Coxis\Core\App::get('cookie')->has('coxis_remember')) {
			$remember = \Coxis\Core\App::get('cookie')->get('coxis_remember');
			$administrator = \Coxis\Admin\Entities\Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			\Coxis\Core\App::get('session')->set('admin_id', $administrator->id);
			if(\Coxis\Core\App::get('post')->get('remember')=='yes')
				\Cookie::set('coxis_remember', md5($administrator->username.'-'.$administrator->password));
			if(\Coxis\Core\App::get('session')->has('redirect_to'))
				return $this->response->redirect(\Coxis\Core\App::get('session')->get('redirect_to'), false);
			else
				return $this->response->redirect('admin');
		}
		elseif(\Coxis\Core\App::get('post')->has('username'))
			\Coxis\Core\App::get('flash')->addError(__('Invalid username or password.'));
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		\Coxis\Core\App::get('cookie')->remove('coxis_remember');
		\Coxis\Core\App::get('session')->remove('admin_id');
		return $this->response->redirect('');
	}
}
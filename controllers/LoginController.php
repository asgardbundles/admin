<?php
namespace Asgard\Admin\Controllers;

class LoginController extends \Asgard\Core\Controller {
	public function configure() {
		$this->layout = false;
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if(\Asgard\Core\App::get('session')->get('admin_id'))
			return $this->response->redirect('admin');
	
		$administrator = null;
		if(\Asgard\Core\App::get('post')->has('username'))
			$administrator = \Asgard\Admin\Entities\Administrator::where(array('username' => \Asgard\Core\App::get('post')->get('username'), 'password' => sha1(\Asgard\Core\App::get('config')->get('salt').\Asgard\Core\App::get('post')->get('password'))))->first();
		elseif(\Asgard\Core\App::get('cookie')->has('asgard_remember')) {
			$remember = \Asgard\Core\App::get('cookie')->get('asgard_remember');
			$administrator = \Asgard\Admin\Entities\Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			\Asgard\Core\App::get('session')->set('admin_id', $administrator->id);
			if(\Asgard\Core\App::get('post')->get('remember')=='yes')
				\Cookie::set('asgard_remember', md5($administrator->username.'-'.$administrator->password));
			if(\Asgard\Core\App::get('session')->has('redirect_to'))
				return $this->response->redirect(\Asgard\Core\App::get('session')->get('redirect_to'), false);
			else
				return $this->response->redirect('admin');
		}
		elseif(\Asgard\Core\App::get('post')->has('username'))
			\Asgard\Core\App::get('flash')->addError(__('Invalid username or password.'));
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		\Asgard\Core\App::get('cookie')->remove('asgard_remember');
		\Asgard\Core\App::get('session')->remove('admin_id');
		return $this->response->redirect('');
	}
}
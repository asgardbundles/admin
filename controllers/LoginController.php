<?php
namespace App\Admin\Controllers;

class LoginController extends \App\Core\Controller {
	public function configure() {
		$this->layout = false;
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if(\Session::get('admin_id'))
			return \Response::redirect('admin');
	
		$administrator = null;
		if(\POST::has('username'))
			$administrator = Administrator::where(array('username' => \POST::get('username'), 'password' => sha1(\Config::get('salt').\POST::get('password'))))->first();
		elseif(\Cookie::has('coxis_remember')) {
			$remember = \Cookie::get('coxis_remember');
			$administrator = Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			\Session::set('admin_id', $administrator->id);
			if(\POST::get('remember')=='yes')
				\Cookie::set('coxis_remember', md5($administrator->username.'-'.$administrator->password));
			if(\SESSION::has('redirect_to'))
				return \Response::redirect(\SESSION::get('redirect_to'), false);
			else
				return \Response::redirect('admin');
		}
		elseif(\POST::has('username'))
			\Flash::addError(__('Invalid username or password.'));
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		\Cookie::remove('coxis_remember');
		\Session::remove('admin_id');
		return \Response::redirect('');
	}
}
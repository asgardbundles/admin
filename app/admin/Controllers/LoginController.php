<?php
namespace App\Admin\Controllers;

class LoginController extends \Asgard\Core\Controller {
	public function configure() {
		$this->layout = false;
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if($this->request->session->get('admin_id'))
			return $this->response->redirect('admin');
	
		$administrator = null;
		if($this->request->post->has('username'))
			$administrator = \App\Admin\Entities\Administrator::where(array('username' => $this->request->post->get('username'), 'password' => sha1(\Asgard\Core\App::get('config')->get('key').$this->request->post->get('password'))))->first();
		elseif($this->request->cookie->has('asgard_remember')) {
			$remember = $this->request->cookie->get('asgard_remember');
			$administrator = \App\Admin\Entities\Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			$this->request->session->set('admin_id', $administrator->id);
			if($this->request->post->get('remember')=='yes')
				$this->request->cookie->set('asgard_remember', md5($administrator->username.'-'.$administrator->password));
			if($this->request->session->has('redirect_to'))
				return $this->response->redirect($this->request->session->get('redirect_to'), false);
			else
				return $this->response->redirect('admin');
		}
		elseif($this->request->post->has('username'))
			\Asgard\Core\App::get('flash')->addError(__('Invalid username or password.'));
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		$this->request->cookie->remove('asgard_remember');
		$this->request->session->remove('admin_id');
		return $this->response->redirect('');
	}
}
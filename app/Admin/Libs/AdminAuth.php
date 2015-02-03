<?php
namespace Admin\Libs;

class AdminAuth implements \Asgard\Auth\IAuth {
	protected $container;

	public function __construct($container) {
		$this->container = $container;
	}

	public function isConnected() {
		return $this->getRequest()->session->has('admin_id');
	}

	public function isGuest() {
		return !$this->isConnected();
	}

	public function check() {
		if(!$this->isConnected())
			throw new \Asgard\Auth\NotAuthenticatedException();
	}

	public function attempt($user, $password) {
		$administrator = \Admin\Entities\Administrator::where(['username' => $user, 'password' => \Admin\Entities\Administrator::hash($password)])->first();
		if(!$administrator)
			$administrator = $this->attemptRemember($user, $password);
		
		if($administrator) {
			$this->connect($administrator->id);
			return $administrator;
		}
		return false;
	}

	public function attemptRemember() {
		$remember = $this->getRequest()->cookie['asgard_remember'];
		return \Admin\Entities\Administrator::where(['MD5(CONCAT(username, \'-\', password))' => $remember])->first();
	}

	public function remember($user, $password) {
		$this->getRequest()->cookie['asgard_remember'] = md5($user.'-'.\Admin\Entities\Administrator::hash($password));
	}

	public function connect($id) {
		$this->getRequest()->session['admin_id'] = $id;
	}

	public function disconnect() {
		unset($this->getRequest()->session['admin_id']);
	}
	
	public function user() {
		return \Admin\Entities\Administrator::load($this->getRequest()->session['admin_id']);
	}

	protected function getRequest() {
		return $this->container['httpKernel']->getRequest();
	}
}
<?php
namespace Admin\Libs;

class AdminAuth implements \Asgard\Auth\IAuth {
	protected $container;

	public function __construct($container) {
		$this->container = $container;
	}

	public function isConnected() {
		return $this->container['session']->has('admin_id');
	}

	public function isGuest() {
		return !$this->isConnected();
	}

	public function check() {
		if(!$this->isConnected())
			throw new \Asgard\Auth\NotAuthenticatedException();
	}

	public function attempt($user, $password) {
		$administrator = \Admin\Entity\Administrator::where(['username' => $user, 'password' => \Admin\Entity\Administrator::hash($password)])->first();
		if(!$administrator)
			$administrator = $this->attemptRemember($user, $password);
		
		if($administrator) {
			$this->connect($administrator->id);
			return $administrator;
		}
		return false;
	}

	public function attemptRemember() {
		$remember = $this->container['cookies']['asgard_remember'];
		return \Admin\Entity\Administrator::where(['MD5(CONCAT(username, \'-\', password))' => $remember])->first();
	}

	public function remember($user, $password) {
		$this->container['cookies']['asgard_remember'] = md5($user.'-'.\Admin\Entity\Administrator::hash($password));
	}

	public function connect($id) {
		$this->container['session']['admin_id'] = $id;
	}

	public function disconnect() {
		unset($this->container['session']['admin_id']);
	}
	
	public function user() {
		return \Admin\Entity\Administrator::load($this->container['session']['admin_id']);
	}

	protected function getRequest() {
		return $this->container['httpKernel']->getRequest();
	}
}
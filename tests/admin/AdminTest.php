<?php
namespace App\Admin\Tests;

class AdminTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		if(!defined('_ENV_'))
			define('_ENV_', 'test');
		require_once(_CORE_DIR_.'core.php');
		\Asgard\Core\App::instance(true)->config->set('bundles', array(
			new \App\Admin\Bundle,
			new \Asgard\Validation\Bundle,
		));
		\Asgard\Core\App::loadDefaultApp();

		\Asgard\Core\App::get('db')->import(__DIR__.'/admin.sql');
	}

// controllers/AdminController.php
// controllers/AdministratorAdminController.php
// controllers/DefaultAdminController.php
// controllers/LoginController.php
// libs/AdminMenu.php
// libs/AsgardAdmin.php
// libs/controller/AdminParentController.php
// libs/controller/EntityAdminController.php
// libs/form/AdminForm.php
// libs/form/AdminEntityForm.php
// libs/form/AdminSimpleForm.php
// libs/form/SimpleAdminForm.php

	public function test0() {
		$browser = new \Asgard\Utils\Browser;

		$this->assertEquals($browser->get('admin')->getCode(), 401); #not allowed

		$this->assertEquals($browser->post('admin/login', array('username'=>'admin', 'password'=>'admin'))->getCode(), 200); #login

		$this->assertEquals($browser->get('admin')->getCode(), 200); #allowed

		$browser->get('admin/logout');
		$this->assertEquals($browser->get('admin')->getCode(), 401); #not allowed
	}
}

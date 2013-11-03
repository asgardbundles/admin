<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(_CORE_DIR_.'core.php');
\Coxis::load();

class AdminTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		DB::import('tests/coxis.sql');
	}

	public function tearDown(){}

// controllers/AdminController.php
// controllers/AdministratorAdminController.php
// controllers/DefaultAdminController.php
// controllers/LoginController.php
// libs/AdminMenu.php
// libs/CoxisAdmin.php
// libs/controller/AdminParentController.php
// libs/controller/ModelAdminController.php
// libs/form/AdminForm.php
// libs/form/AdminModelForm.php
// libs/form/AdminSimpleForm.php
// libs/form/SimpleAdminForm.php

	public function test0() {
	}
}

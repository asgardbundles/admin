<?php
namespace Tests;

class AdminTest extends \Asgard\Core\Test {
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

	public function test() {
		$browser = $this->getBrowser();

		$this->assertEquals($browser->get('admin')->getCode(), 401, 'GET admin'); #not allowed

		$this->assertTrue($browser->post('admin/login', array('username'=>'admin', 'password'=>'admin'))->isOK(), 'GET admin/login'); #login

		$this->assertTrue($browser->get('admin')->isOK(), 'GET admin'); #allowed
		$this->assertTrue($browser->get('admin/preferences')->isOK(), 'GET admin/preferences');

		$this->assertTrue($browser->get('admin/logout')->isOK(), 'GET admin/logout'); #allowed
		$this->assertEquals($browser->get('admin')->getCode(), 401); #not allowed

		#Administrators
		$browser = $this->getBrowser();
		$browser->setSession('admin_id', 1);
		$this->assertTrue($browser->get('admin/administrators')->isOK(), 'GET admin/administrators');
		$this->assertTrue($browser->get('admin/administrators/1/edit')->isOK(), 'GET admin/administrators/:id/edit');
		$this->assertTrue($browser->get('admin/administrators/new')->isOK(), 'GET admin/administrators/new');

		\App\Admin\Entities\Administrator::create(array('id'=>2, 'username'=>'bob', 'password'=>'bob'));
		$this->assertTrue($browser->get('admin/administrators/2/delete')->isOK(), 'GET admin/administrators/:id/delete');
	}
}

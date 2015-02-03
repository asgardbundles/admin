<?php
class AdminTest extends \Asgard\Http\Test {
	public function test() {
		$browser = $this->createBrowser();

		$this->assertEquals(401, $browser->get('admin')->getCode(), 'GET admin'); #not allowed

		$browser->get('admin/login'); #login
		$formParser = new \Asgard\Http\Browser\FormParser();
		$formParser->parse($browser->getLast()->getContent(), '//*[@id="hld"]/div/div/div[2]/form');
		$formParser->get('username')->setValue('admin');
		$formParser->get('password')->setValue('admin');
		$this->assertTrue($browser->post('admin/login', $formParser->values())->isOK(), 'GET admin/login'); #login

		$this->assertTrue($browser->get('admin')->isOK(), 'GET admin'); #allowed
		$this->assertTrue($browser->get('admin/preferences')->isOK(), 'GET admin/preferences'); #allowed

		$this->assertTrue($browser->get('admin/logout')->isOK(), 'GET admin/logout'); #allowed
		$this->assertEquals(401, $browser->get('admin')->getCode(), 'GET admin'); #not allowed

		#Administrators
		$browser = $this->createBrowser();
		$browser->getSession()->set('admin_id', 1);
		$this->assertTrue($browser->get('admin/administrators')->isOK(), 'GET admin/administrators');
		$this->assertTrue($browser->get('admin/administrators/1/edit')->isOK(), 'GET admin/administrators/:id/edit');
		$this->assertTrue($browser->get('admin/administrators/new')->isOK(), 'GET admin/administrators/new');

		\Admin\Entities\Administrator::create(['id'=>2, 'username'=>'bob', 'password'=>'bob']);
		$this->assertTrue($browser->get('admin/administrators/2/delete')->isOK(), 'GET admin/administrators/:id/delete');
	}
}

<?php
namespace Asgard\Admin\Controllers;

/**
@Prefix('admin/administrators')
*/
class AdministratorAdminController extends \Asgard\Admin\Libs\Controller\EntityAdminController {
	static $_entity = 'Asgard\Admin\Entities\Administrator';
	
	public function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Administrator modified with success.'),
			'created'				=>	__('Administrator created with success.'),
			'many_deleted'	=>	__('%s administrators deleted.'),
			'deleted'				=>	__('Administrator deleted with succes.'),
			'unexisting'			=>	__('This administrator does not exist.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($entity) {
		$form = new AdminEntityForm($entity, $this);
		$form->password->params['view']['value'] = '';
		
		return $form;
	}
}
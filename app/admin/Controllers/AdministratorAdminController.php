<?php
namespace Admin\Controllers;

/**
 * @Prefix("admin/administrators")
 */
class AdministratorAdminController extends \Admin\Libs\Controller\EntityAdminController {
	protected $_entity = 'Admin\Entities\Administrator';
	
	public function __construct() {
		$this->_messages = [
			'modified'			=>	__('Administrator modified with success.'),
			'created'				=>	__('Administrator created with success.'),
			'many_deleted'	=>	__('%s administrators deleted.'),
			'deleted'				=>	__('Administrator deleted with succes.'),
			'unexisting'			=>	__('This administrator does not exist.'),
		];
		parent::__construct();
	}
	
	public function formConfigure($entity) {
		$form = $this->app->make('adminEntityForm', [$entity, $this]);
		$form->password->params['view']['value'] = '';
		
		return $form;
	}
}
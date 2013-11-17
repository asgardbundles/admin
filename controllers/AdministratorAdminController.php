<?php
namespace Coxis\Admin\Controllers;

/**
@Prefix('admin/administrators')
*/
class AdministratorAdminController extends \Coxis\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'administrator';
	
	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Administrator modified with success.'),
			'created'				=>	__('Administrator created with success.'),
			'many_deleted'	=>	__('%s administrators deleted.'),
			'deleted'				=>	__('Administrator deleted with succes.'),
			'unexisting'			=>	__('Cet administrator does not exist.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model, $this);
		$form->password->params['view']['value'] = '';
		
		return $form;
	}
}
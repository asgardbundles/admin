<?php
namespace Coxis\Admin\Controllers;

/**
@Prefix('admin/preferences')
*/
class PreferencesAdminController extends \Coxis\Admin\Libs\Controller\AdminParentController {
	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Preferences modified with success.'),
		);
	}
	
	public function formConfigure() {
		$form = new \Coxis\Admin\Libs\Form\AdminSimpleForm($this, 'preferences');
		
		$form->values = array();
		$vars = array('email', 'head_script');
		foreach($vars as $valueName) {
			$value = \Coxis\Value\Entities\Data::fetch($valueName);
			$a = new \Coxis\Admin\Libs\Form\AdminEntityForm($value, $this);
			unset($a->key);
			$form->values[$value->key] = $a;
		}
		
		return $form;
	}
	
	/**
	@Route('')
	*/
	public function editAction($request) {
		$this->form = $this->formConfigure();
	
		if($this->form->isSent()) {
			try {
				$this->form->save();
				\Coxis\Core\App::get('flash')->addSuccess($this->_messages['modified']);
				if(\Coxis\Core\App::get('post')->has('send'))
					return \Coxis\Core\App::get('response')->back();
			} catch(\Coxis\Form\FormException $e) {}
		}
		
		$this->setRelativeView('form.php');
	}
}
?>
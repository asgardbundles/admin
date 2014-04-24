<?php
namespace App\Admin\Controllers;

/**
@Prefix('admin/preferences')
*/
class PreferencesAdminController extends \App\Admin\Libs\Controller\AdminParentController {
	public function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Preferences modified with success.'),
		);
	}
	
	public function formConfigure() {
		$form = new \App\Admin\Libs\Form\AdminSimpleForm($this, 'preferences');
		
		$form->values = array();
		$vars = array('email', 'head_script');
		foreach($vars as $valueName) {
			$value = \Asgard\Data\Entities\Data::fetch($valueName);
			$a = new \App\Admin\Libs\Form\AdminEntityForm($value, $this);
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
				\Asgard\Core\App::get('flash')->addSuccess($this->_messages['modified']);
				if(\Asgard\Core\App::get('post')->has('send'))
					return \Asgard\Core\App::get('response')->back();
			} catch(\Asgard\Form\FormException $e) {}
		}
		
		$this->setRelativeView('form.php');
	}
}
?>
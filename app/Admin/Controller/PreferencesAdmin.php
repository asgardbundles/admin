<?php
namespace Admin\Controller;

/**
 * @Prefix("admin/preferences")
 */
class PreferencesAdmin extends \Admin\Libs\Controller\AdminParentController {
	public function __construct() {
		$this->_messages = [
			'modified'			=>	__('Preferences modified with success.'),
		];
	}
	
	public function formConfigure() {
		$form = $this->container->make('adminSimpleForm', [$this, 'preferences']);
		
		$data = $this->container['data'];
		$form['email'] = new \Asgard\Form\Field\TextField([
			'default'    => $data->get('email'),
			'validation' => ['email']
		]);
		$form['head_script'] = new \Asgard\Form\Field\TextField(['default'=>$data->get('head_script')]);
		$form->setSaveCallback(function($chain, $form) use($data) {
			$data->set('email', $form['email']->value());
			$data->set('head_script', $form['head_script']->value());
		});
		
		return $form;
	}
	
	/**
	 * @Route("")
	 */
	public function editAction(\Asgard\Http\Request $request) {
		$this->form = $this->formConfigure();
	
		if($this->form->sent()) {
			try {
				$this->form->save();
				$this->getFlash()->addSuccess($this->_messages['modified']);
				if($request->post->has('send'))
					return $this->back();
			} catch(\Asgard\Form\FormException $e) {}
		}
		
		$this->view = 'form';
	}
}

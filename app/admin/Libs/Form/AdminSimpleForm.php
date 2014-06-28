<?php
namespace Admin\Libs\Form;

class AdminSimpleForm extends \Asgard\Form\Form {
	use AdminFormTrait;

	public function __construct(\Asgard\Http\Controller $controller, $name=null, array $params=[], $widgetsManager=null) {
		parent::__construct($name, $params, $controller->request);
		$this->construct($controller, $widgetsManager);
	}

	protected function isRequired($name, $options) {
		return isset($options['validation']['required']) && $options['validation']['required'];
	}
}
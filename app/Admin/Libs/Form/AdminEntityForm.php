<?php
namespace Admin\Libs\Form;

class AdminEntityForm extends \Asgard\Entityform\EntityForm {
	use AdminFormTrait;

	public function __construct(\Asgard\Entity\Entity $entity,
		\Asgard\Http\Controller $controller,
		array $params=[],
		$widgetsManager=null,
		$entityFieldsSolver=null,
		\Asgard\Orm\DataMapper $dataMapper=null) {
		parent::__construct($entity, $params, $controller->request, $entityFieldsSolver, $dataMapper);
		$this->construct($controller, $widgetsManager);
	}

	protected function isRequired($name, $options) {
		if(isset($options['validation']['required']) && $options['validation']['required'])
			return true;
		return $this->getEntity()->getDefinition()->hasProperty($name) && $this->getEntity()->getDefinition()->property($name)->required();
	}
}
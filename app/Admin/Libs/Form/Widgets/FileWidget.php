<?php
namespace Admin\Libs\Form\Widgets;

class FileWidget extends \Asgard\Form\Widget {
	public function render(array $options=[]) {
		$options = $this->options+$options;
		
		$attrs = [];
		if(isset($options['attrs']))
			$attrs = $options['attrs'];

		$str = \Asgard\Form\HTMLHelper::tag('input', [
			'type'	=>	'file',
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		]+$attrs);
		$container = $this->field->getParent()->getcontainer();
		$entity = $this->field->getParent()->getEntity();
		$name = $this->field->name;		
		$optional = !$entity->property($name)->required();

		if($entity->isOld() && $entity->$name && $entity->$name->exists()) {
			$path = $entity->$name->srcFromWebDir();
			if(!$path || $entity->$name->isUploaded())
				return $str;
			$str .= '<p>
				<a target="_blank" href="'.$container['request']->url->to($path).'">'.__('Download').'</a>
			</p>';
			
			if($optional)
				$str .= '<a href="'.$container['resolver']->url_for(['Admin\Controllers\FilesController', 'delete'], ['entityAlias' => $container['adminManager']->getAlias(get_class($entity)), 'id' => $entity->id, 'file' => $name]).'">'. __('Delete').'</a><br/><br/>';
		}

		return $str;
	}
}
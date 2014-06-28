<?php
namespace Admin\Libs\Form\Widgets;

class ImageWidget extends \Asgard\Form\Widget {
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
		$app = $this->field->getTopForm()->getApp();
		$entity = $this->field->getTopForm()->getEntity();
		$name = $this->field->name;		
		$optional = !$entity->getDefinition()->property($name)->required();

		if($entity->isOld() && $entity->$name && $entity->$name->exists()) {
			$path = $entity->$name->srcFromWebDir();
			if(!$path || $entity->$name->isUploaded())
				return $str;
			$str .= '<p>
				<a target="_blank" href="'.$app['request']->url->to($path).'" rel="facebox"><img src="'.$app['imagecache']->url($path, 'admin_thumb').'" alt=""/></a>
			</p>';
			
			if($optional)
				$str .= '<a href="'.$app['resolver']->url_for(['Admin\Controllers\FilesController', 'delete'], ['entityAlias' => $app['adminManager']->getAlias(get_class($entity)), 'id' => $entity->id, 'file' => $name]).'">'. __('Delete').'</a><br/><br/>';
		}

		return $str;
	}
}
<?php
namespace Asgard\Admin\Libs\Form\Widgets;

class FileWidget extends \Asgard\Form\Widgets\HTMLWidget {
	public function render($options=array()) {
		$options = $this->options+$options;
		
		$attrs = array();
		if(isset($options['attrs']))
			$attrs = $options['attrs'];

		$str = \Asgard\Form\HTMLHelper::tag('input', array(
			'type'	=>	'file',
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		)+$attrs);
		$entity = $this->field->form->getEntity();
		$name = $this->field->name;		
		$optional = !$entity->property($name)->required;

		if($entity->isOld() && $entity->$name && $entity->$name->exists()) {
			$path = $entity->$name->get();
			if(!$path || !$entity->$name->saved)
				return $str;
			if($entity->property($name)->filetype == 'image') {
				$str .= '<p>
					<a target="_blank" href="../'.$path.'" rel="facebox"><img src="'.\Asgard\Core\App::get('url')->to(\Asgard\ImageCache\Libs\ImageCache::src($path, 'admin_thumb')).'" alt=""/></a>
				</p>';
			}
			else {
				$str .= '<p>
					<a target="_blank" href="../'.$path.'">'.__('Download').'</a>
				</p>';
			}
			
			if($optional) {
				try {
					$str .= '<a href="'.$this->field->form->controller->url_for('deleteSingleFile', array('file'=>$name, 'id'=>$entity->id)).'">'. __('Delete').'</a><br/><br/>';
				} catch(\Exception $e) {}
			}
		}

		return $str;
	}
}
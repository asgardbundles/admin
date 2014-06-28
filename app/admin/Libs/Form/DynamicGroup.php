<?php
namespace Admin\Libs\Form;

class DynamicGroup extends \Asgard\Form\DynamicGroup {
	public function def(array $options=[]) {
		return $this->parent->render('group', $this, $options);
	}

	protected function renderField($field) {
		return __($field->def(['label'=>false]));
	}

	public function label() {
		return ucfirst(str_replace('_', ' ', $this->name));
	}

	public function render($render_callback, $field, array $options=[]) {
		switch($render_callback) {
			case 'text':
			case 'textarea':
				return $this->doRender($render_callback, $field, $options);
		}

		return $this->parent->render($render_callback, $field, $options);
	}

	protected function doRender($render_callback, $field, &$options) {
		$options['attrs']['style'] = "width:80%";
		$widget = parent::doRender($render_callback, $field, $options);

		$str = '';
		if($error=$field->error())
			$str .= '<span class="error">'.$error.'</span>';
		$str .= $widget->render();

		return $str;
	}
}
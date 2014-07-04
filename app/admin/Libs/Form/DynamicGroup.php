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
		return $this->parent->render($render_callback, $field, $options);
	}
}
<?php
namespace Admin\Libs\Form;

trait AdminFormTrait {
	public $controller;

	protected function construct(\Asgard\Http\Controller $controller, $widgetManager) {
		$this->controller = $controller;

		$this->widgetManager = $widgetManager;
		$this->widgetManager->addNamespace('Admin\Libs\Form\Widgets');

		$this->widgetManager->setWidget('text', function(\Asgard\Form\Field $field, array $options) {
			$options['attrs']['class'] = 'text big';
			return $field->getTopForm()->getWidget('Asgard\Form\Widgets\TextWidget', $field->name(), $field->value(), $options);
		});
		$this->widgetManager->setWidget('textarea', function(\Asgard\Form\Field $field, array $options) {
			$options['attrs']['class'] = 'text big';
			return $field->getTopForm()->getWidget('Asgard\Form\Widgets\TextareaWidget', $field->name(), $field->value(), $options);
		});
		$this->widgetManager->setWidget('password', function(\Asgard\Form\Field $field, array $options) {
			$options['attrs']['class'] = 'text big';
			return $field->getTopForm()->getWidget('Asgard\Form\Widgets\PasswordWidget', $field->name(), $field->value(), $options);
		});
		$this->widgetManager->setWidget('select', function(\Asgard\Form\Field $field, array $options) {
			$options['attrs']['class'] = 'styled';
			return $field->getTopForm()->getWidget('Asgard\Form\Widgets\SelectWidget', $field->name(), $field->value(), $options);
		});
		$this->widgetManager->setWidget('date', function(\Asgard\Form\Field $field, array $options) {
			$options['attrs']['class'] = 'text big';
			return $field->getTopForm()->getWidget('Asgard\Form\Widgets\DateWidget', $field->name(), $field->value(), $options);
		});
	}
	
	protected function doRender($render_callback, $field, array &$options) {
		$widget = parent::doRender($render_callback, $field, $options);

		if($field instanceof \Asgard\Form\Fields\HiddenField)
			return $widget;
		elseif($field instanceof \Admin\Libs\Form\Fields\MultipleFilesField)
			return $widget->render();
		elseif($field instanceof \Asgard\Form\Group)
			return $widget->render();

		$str = '<p>';
		if(!isset($options['label']) || $options['label']!==false) {
			$label = $field->label();
			if(isset($options['label']))
				$label = $options['label'];

			$label = __($label);
			$name = $field->getName();
			if($this->isRequired($name, $options))
				$label .= '*';

			$str .= '<label for="'.$options['id'].'">'.$label.'</label>';
		}

		if($error=$field->error())
			$str .= '<span class="error">'.$error.'</span>';
		if(isset($options['note']))
			$str .= '<span class="note">'.$options['note'].'</span>';
		$str .= $widget->render().'
		</p>';

		return $str;
	}

	public function showErrors() {
		if(!$this->errors)
			return;
		$error_found = false;
		foreach($this->errors as $field_name=>$errors) {
			if(!$this->has($field_name) || $this->{$field_name} instanceof \Asgard\Form\Fields\HiddenField) {
				if(!$error_found) {
					echo '<div class="flash error">';
					$error_found = true;
				}
				if(is_array($errors)) {
					foreach(\Asgard\Common\ArrayUtils::flateArray($errors) as $error)
						echo '<p>'.$error.'</p>';
				}
				else
					echo '<p>'.$errors.'</p>';
			}
		}
		if($error_found)
			echo '</div>';
	}

	public function h3($title) {
		return '<h3>'.$title.'</h3>';
	}

	public function h4($title) {
		return '<h3>'.$title.'</h3>';
	}

	public function close($submits=null) {
		$r = '<hr/>';
		if($submits === null)
			$r .= '<p>
				'.$this->getWidget('Asgard\Form\Widgets\SubmitWidget', 'stay', __('Save'), ['attrs' => ['class' => 'submit long']])->render().'
				'.$this->getWidget('Asgard\Form\Widgets\SubmitWidget', 'send', __('Save & Leave'), ['attrs' => ['class' => 'submit long']])->render().'
			</p>';
		else
			$r .= $submits;
		$r .= parent::close();
		
		return $r;
	}
}
<?php
namespace Asgard\Admin\Controllers;

class AdminController extends \Asgard\Core\Controller {
	public function layout($content) {
		$this->content = $content;
		$this->setRelativeView('layout.php');
	}
}
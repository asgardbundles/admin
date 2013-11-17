<?php
namespace Coxis\Admin\Controllers;

class AdminController extends \Coxis\Core\Controller {
	public function layout($content) {
		$this->content = $content;
		$this->setRelativeView('layout.php');
	}
}
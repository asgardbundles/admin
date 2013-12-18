<?php
namespace Coxis\Admin\Libs;

class CoxisAdmin {
	public static function getEntityFor($controller) {
		return $controller::getEntity();
	}

	public static function getIndexURLFor($controller) {
		return $controller::getIndexURL();
	}

	public static function getEditURLFor($controller, $id) {
		return $controller::getEditURL($id);
	}
}
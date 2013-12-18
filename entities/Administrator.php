<?php
namespace Coxis\Admin\Entities;

class Administrator extends \Coxis\Core\Entity {
	public static $properties = array(
		'username'    => array(
			'length'    =>    100,
			'unique'	=>	true,
		),
		'password'    => array(
			'form'	=>	array(
				'hidden'	=>	true,
			),
			'length'    =>    100,
			'setHook'  =>    array('tools', 'hash'),
		),
	);

	#General
	public function __toString() {
		return $this->username;
	}

	public static $behaviors = array();
	public static $relations = array();
		
	public static $meta = array(
	);

	public static function configure($definition) {
		$definition->hookBefore('destroy', function($chain, $entity) {
			if(Administrator::count() < 2)
				$chain->stop();
		});
	}
}
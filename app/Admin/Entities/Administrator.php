<?php
namespace Admin\Entities;

class Administrator extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\EntityDefinition $definition) {
		$definition->properties = [
			'username'    => [
				'validation' => [
					'unique'	=>	true,
				],
				'required' => true,
			],
			'email' => 'email',
			'password'    => [
				'setHook'  =>    ['Admin\Entities\Administrator', 'hash'],
				'form'	=>	[
					'hidden'	=>	true,
				],
			],
		];

		$definition->behaviors = [
			new \Asgard\Orm\ORMBehavior
		];
		
		$definition->hookBefore('destroy', function(\Asgard\Hook\HookChain $chain, \Asgard\Entity\Entity $entity) {
			if(Administrator::count() < 2)
				$chain->stop();
		});
	}

	#General
	public function __toString() {
		return $this->username;
	}

	public static function hash($pwd) {
		return sha1(static::getapp()->get('config')->get('key').$pwd);
	}
}
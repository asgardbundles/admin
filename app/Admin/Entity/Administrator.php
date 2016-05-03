<?php
namespace Admin\Entity;

class Administrator extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'username' => [
				'validation' => [
					'unique' => true,
				],
				'required' => true,
			],
			'email' => 'email',
			'password' => [
				'hooks' => [
					'set' => ['Admin\Entity\Administrator', 'hash'],
				],
				'form' => [
					'hidden' => true,
				],
			],
		];

		$definition->behaviors = [
			new \Asgard\Orm\ORMBehavior
		];
		
		$definition->preHook('destroy', function(\Asgard\Hook\Chain $chain, \Asgard\Entity\Entity $entity) {
			if(Administrator::count() < 2)
				$chain->stop();
		});
	}

	#General
	public function __toString() {
		return $this->username;
	}

	public static function hash($pwd) {
		return sha1(static::getContainer()->get('config')->get('key').$pwd);
	}
}
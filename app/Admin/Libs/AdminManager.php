<?php
namespace Admin\Libs;

class AdminManager {
	protected $aliases = [];

	public function setAlias($alias, $class) {
		$this->aliases[$alias] = $class;
		return $this;
	}

	public function getClass($alias) {
		return $this->aliases[$alias];
	}

	public function getAlias($class) {
		foreach($this->aliases as $alias=>$_class) {
			if($_class == $class)
				return $alias;
		}
	}
}
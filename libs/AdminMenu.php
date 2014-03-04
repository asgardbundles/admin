<?php
namespace Asgard\Admin\Libs;

class AdminMenu {
	protected static $instance;

	public $menu = array();
	public $home = array();

	public static function instance() {
		if(!static::$instance)
			static::$instance = new static;
		return static::$instance;
	}

	function __construct() {
		$this->menu = array(array(
			'label'	=>	__('Content'),
			'link'	=>	'#',
			'childs'	=>	array()
		));
	}

	public function showMenu($menu=null) {
		if($menu === null)
			$menu = $this->menu;
		foreach($menu as $item) {
			if(is_array($item))
			?>
			<li><a href="<?php echo $item['link'] ?>"><?php echo $item['label'] ?></a>
			<?php
			if(isset($item['childs']) && $item['childs']) {
				echo '<ul>';
				$this->showMenu($item['childs']);
				echo '</ul>';
			}
			?>
			</li>
			<?php
		}
	}
}
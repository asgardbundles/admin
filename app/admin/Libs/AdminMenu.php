<?php
namespace Admin\Libs;

class AdminMenu {
	protected $menu = [];
	protected $home = [];

	public function __construct() {
		$this->menu = [[
			'label'	=>	__('Content'),
			'link'	=>	'#',
			'childs'	=>	[]
		]];
	}

	public function add($link, $position) {
		$position = explode('.', $position);
		$last = array_pop($position);
		$menu = &$this->menu;
		foreach($position as $step)
			$menu = &$menu[$step]['childs'];
		if($last === '')
			$menu[] = $link;
		else
			$menu[$last] = $link;
		return $this;
	}

	public function showMenu($menu=null) {
		if($menu === null)
			$menu = $this->menu;
		foreach($menu as $item) {
			?>
			<li><a href="<?=$item['link'] ?>"><?=$item['label'] ?></a>
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

	public function addHome($link) {
		$this->home[] = $link;
		return $this;
	}

	public function getHome() {
		return $this->home;
	}
}
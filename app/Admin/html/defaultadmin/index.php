<style>
#dash-menu {
	width:900px;
	margin:0 auto;
}
#dash-menu li {
	float:left;
	list-style-type: none;
	width:200px;
	text-align: center;
	margin:10px;
}
#dash-menu li img, #dash-menu li embed {
	width:100px;
	height:100px;
	display:block;
	margin:0 auto;
}
#dash-menu li .title {
	display:block;
}
#dash-menu li .description {
	display:block;
	
}
</style>

<ul id="dash-menu">
	<?php $this->getFlash()->showAll() ?>
	<?php foreach($this->container['adminMenu']->getHome() as $link): ?>
	<li>
		<a href="<?=$link['link'] ?>"><img src="<?=$link['img'] ?>"></a>
		<a class="title" href="<?=$link['link'] ?>"><?=$link['title'] ?></a>
		<span class="description"><?=$link['description'] ?></span>
	</li>
	<?php endforeach ?>
</ul>
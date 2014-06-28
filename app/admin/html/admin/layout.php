<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$controller->getApp()['data']['name'] ?> &#9679; <?=__('Administration') ?></title>
	<base href="<?=$controller->request->url->to('admin/') ?>" />
	<style type="text/css" media="all">
		@import url("../bundles/admin/css/admin.css");
		@import url("../bundles/admin/css/jquery.wysiwyg.css");
		@import url("../bundles/admin/css/facebox.css");
		@import url("../bundles/admin/css/visualize.css");
		@import url("../bundles/admin/css/date_input.css");
	</style>
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("../bundles/admin/css/ie.css");</style><![endif]-->
	<script type="text/javascript" src="../js/jquery.js"></script>
			<script src="<?=$controller->request->url->to('bundles/admin/js/jquery-ui.min.js') ?>" type="text/javascript"></script>
			<link rel="stylesheet" href="<?=$controller->request->url->to('bundles/admin/css/jquery-ui.css') ?>" type="text/css" media="all" />
			<link rel="stylesheet" href="<?=$controller->request->url->to('bundles/admin/css/ui.theme.css') ?>" type="text/css" media="all" />
	<script>
	window.i18n = {
		'admin': {
			'are_you_sure': '<?=addcslashes(__('Are you sure?'), '\'') ?>',
		}
	};
	</script>
</head>
<body>
	<div id="hld">
		<div class="wrapper">	
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				<h1><a href=".."><?=$controller->getApp()['data']['name'] ?></a></h1>
				
				<ul id="nav">
					<li><a href="#"><?=__('Dashboard') ?></a></li>
					<?php
					$controller->getApp()['adminMenu']->showMenu();
					?>
				</ul>
				<p class="user"><a href=".."><?=__('See website') ?></a> | <a href="logout"><?=__('Disconnect') ?></a></p>
			</div>	
			
			<?=$content; ?>
			
			<div id="footer">
				<p class="left"><?=$controller->getApp()['config']['admin.footer'] ?></p>
			</div>
		</div>			
	</div>	

	<!--[if IE]><script type="text/javascript" src="../bundles/admin/js/excanvas.js"></script><![endif]-->	
	<script type="text/javascript" src="../bundles/admin/js/jquery.img.preload.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.filestyle.mini.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.date_input.pack.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/facebox.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.visualize.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.visualize.tooltip.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.select_skin.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/ajaxupload.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.pngfix.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/custom.js"></script>
	
	<?php $controller->getApp()['html']->printAll() ?>
</body>
</html>
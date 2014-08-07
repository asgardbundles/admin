<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$this->container['config']['website.name'] ?> &#9679; Login</title>
	<base href="<?=$this->request->url->to('admin/') ?>" />
    <style type="text/css" media="all">
		@import url("<?=$this->request->url->to('bundles/admin/css/admin.css') ?>");
    </style>
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("<?=$this->request->url->to('css/ie.css') ?>");</style><![endif]-->
</head>
<body>
	<div id="hld">
		<div class="wrapper">		<!-- wrapper begins -->
			<div class="block small center login">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Login</h2>
					<ul>
						<li><a href="<?=$this->request->url->to('') ?>"><?=__('back to website') ?></a></li>
					</ul>
				</div>		<!-- .block_head ends -->
				<div class="block_content">
					<?php $this->getFlash()->showAll() ?>
					<form action="login" method="post">
						<p>
							<label><?=__('Username:') ?></label> <br />
							<input type="text" class="text" name="username"/>
						</p>
						<p>
							<label><?=__('Password:') ?></label> <br />
							<input type="password" class="text" name="password"/> (<a href="forgotten"><?=__('Password forgotten?') ?></a>)
						</p>
						<p>
							<input type="submit" class="submit" value="Login" /> <span><label for="remember"><?=__('Remember me') ?></label> <input type="checkbox" id="remember" name="remember" value="yes"/></span>
						</p>
					</form>
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .login ends -->
		</div>						<!-- wrapper ends -->
	</div>		<!-- #hld ends -->
	
</body>
</html>
			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2><?=!$administrator->isNew() ? $administrator:__('New') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
					<p class="breadcrumb"><a href="administrators"><?=__('Administrators') ?></a> &raquo; 
					<a href="<?=!$administrator->isNew() ? 'administrators/'.$administrator->id.'/edit':'administrators/new' ?>">
					<?=!$administrator->isNew() ? $administrator:__('New') ?>
					</a></p>
				
					<?php $this->getFlash()->showAll() ?>
					
					<?php
					echo $form->open();
					echo
						$form['username']->def(['label'	=>	__('Username')]).
						$form['password']->password(['label'	=>	__('Password')]).
						$form['email']->def(['label'	=>	__('Email')]);
					echo $form->close();
					?>
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>		<!-- .block ends -->
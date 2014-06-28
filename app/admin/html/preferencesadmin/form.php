			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>	
					<h2><?=__('Preferences') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="preferences"><?=__('Preferences') ?></a></p>
				
					<?php $this->getFlash()->showAll() ?>
					
					<?php
					echo $form->open();
					echo
						$form['email']->def(['label'=>__('Email')]).
						$form['head_script']->textarea(['label'=>__('Script')]);
					echo $form->close();
					?>
					
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->
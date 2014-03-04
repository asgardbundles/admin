			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>	
					<h2><?php echo __('Preferences') ?></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="preferences"><?php echo __('Preferences') ?></a></p>
				
					<?php \Coxis\Core\App::get('flash')->showSuccess() ?>
					<?php $form->showErrors() ?>
					
					<?php
					$form->open();
					echo
						$form->values['email']->value->def(array('label'=>__('Email'))).
						$form->values['head_script']->value->textarea(array('label'=>__('Script')));
					$form->close();
					?>
					
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->
			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><a href="<?=$this->url_for('index') ?>"><?=__('Administrators') ?></a></h2>
					<ul>
						<li><a href="administrators/new"><?=__('Add') ?></a></li>
					</ul>
					<?php
					echo $searchForm->open();
					echo $searchForm['search']->def([
						'attrs'	=>	[
							'class'	=>	'text',
							'placeholder'	=>	'Search',
						],
					]);
					echo $searchForm->close();
					?>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<?php $this->getFlash()->showAll() ?>
				
					<?php if(count($administrators) == 0): ?>
					<div style="text-align:center; font-weight:bold"><?=__('No element') ?></div>
					<?php else: ?>
					<form action="" method="post">
						<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
							<thead>
								<tr>
									<th width="10"><input type="checkbox" class="check_all" /></th>
									<th><?=__('Username') ?></th>
									<td>&nbsp;</td>
								</tr>
							</thead>
							
							<tbody>
								<?php
								foreach($administrators as $administrator) { ?>								
									<tr>
										<td><input type="checkbox" name="id[]" value="<?=$administrator->id ?>" /></td>
										<td><a href="administrators/<?=$administrator->id ?>/edit"><?=$administrator ?></a></td>
											<td class="actions">
													<?php $this->app['hooks']->trigger('asgard_administrator_actions', [$administrator]) ?>
													<a class="delete" href="administrators/<?=$administrator->id ?>/delete"><?=__('Delete') ?></a>
												</td>
									</tr>
								<?php } ?>
							</tbody>
							
						</table>
						
						<div class="tableactions">
							<select name="action">
								<option><?=__('Actions') ?></option>
								<option value="delete"><?=__('Delete') ?></option>
							</select>
							
							<input type="submit" class="submit tiny" value="<?=__('Apply') ?>" />
						</div>		<!-- .tableactions ends -->
						
						<?php
						if(isset($paginator))
						if($paginator->getPages()>1) {
						?>
						<div class="pagination right">
							<?php $paginator->show() ?>
						</div>
						<?php
						}
						?>
						
					</form>
							<?php endif ?>
						</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		
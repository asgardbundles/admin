<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><% echo !$original->isNew() ? $original:__('New')%></h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
					<p class="breadcrumb"><a href="<% echo $this->url('index')%>"><%=__(ucfirst($this->_plural))%></a> &raquo; 
					<a href="<% echo !$<?=$entity['meta']['name'] ?>->isNew() ? $this->url('edit', ['id'=>$<?=$entity['meta']['name'] ?>->id]):$this->url('new')%>">
					<% echo !$original->isNew() ? $original:__('New')%>
					</a></p>
					<% $this->getFlash()->showAll()%>
					
					<%
					echo $form->open();
<?php foreach($entity['form'] as $field=>$params): ?><?php if(!isset($params['type']) || $params['type']!=='type'): ?>
					echo $form['<?=$field ?>']-><?=$params['render'] ?>(<?php if($params['params']): ?>[<?php
									 foreach($params['params'] as $k=>$v): ?>
										'<?=$k ?>'	=>	<?=static::outputPHP($v) ?>, <?php endforeach ?>]<?php endif ?>);
<?php endforeach ?>
					echo $form->close();
					%>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->
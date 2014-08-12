<?php
namespace Admin\Libs\Form\Widgets;

class FilesWidget extends \Asgard\Form\Widget {
	public function render(array $options=[]) {
		$options = $this->options+$options;
		
		$attrs = [];
		if(isset($options['attrs']))
			$attrs = $options['attrs'];

		$str = \Asgard\Form\HTMLHelper::tag('input', [
			'type'	=>	'file',
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		]+$attrs);
		$container = $this->field->getParent()->getcontainer();
		$entity = $this->field->getParent()->getEntity();
		$name = $this->field->name;		
		$optional = !$entity->property($name)->required();

		if($entity->isNew())
			return null;
		$uid = \Asgard\Common\Tools::randstr(10);
		$container['html']->codeJS("
			$(function(){
				multiple_upload('$uid', '".$container['resolver']->url_for(['Admin\Controllers\FilesController', 'add'], ['entityAlias' => $container['adminManager']->getAlias(get_class($entity)), 'id' => $entity->id, 'file' => $name])."');
			});");
		$container['html']->includeJS('bundles/admin/uploadify/jquery.uploadify.min.js');
		$container['html']->includeJS('bundles/admin/js/uploadify.php');
		$container['html']->includeCSS('bundles/admin/uploadify/uploadify.css');
		ob_start();
		?>
		<div class="block">
		
			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				
				<h2><?=$name ?></h2>
				<?php
				if(isset($options['nb']))
					echo '<span>'.$options['nb'].'</span>';
				?>
			</div>		<!-- .block_head ends -->
			
			<div class="block_content">
				<script>
				window.parentID = <?=$entity->id ?>;
				</script>
				<ul class="list">
					<?php
					$i=1;
					foreach($entity->$name as $file):
					?>
					<li>
						<a href="<?=$container['resolver']->url_for(['Admin\Controllers\FilesController', 'downloadOne'], ['entityAlias' => $container['adminManager']->getAlias(get_class($entity)), 'id' => $entity->id, 'pos' => $i, 'file' => $name]) ?>"><?=__('Download')?></a>
						 | <a href="<?=$container['resolver']->url_for(['Admin\Controllers\FilesController', 'deleteOne'], ['entityAlias' => $container['adminManager']->getAlias(get_class($entity)), 'id' => $entity->id, 'pos' => $i, 'file' => $name]) ?>"><?=__('Delete')?></a>
					</li>
					<?php
					$i++;
					endforeach;
					?>
					</li>
					
				</ul>
				
				<p id="<?=$uid ?>">
					<label><?=__('Upload: ') ?></label><br />
					<input type="file" id="<?=$uid ?>-filesupload" class="filesupload" /><br/>
					<span class="uploadmsg"><?=__('Maximum size 3Mb') ?></span>
					<div id="<?=$uid ?>-custom-queue"></div>
				</p>
				
			</div>		<!-- .block_content ends -->
			
			<div class="bendl"></div>
			<div class="bendr"></div>
			
		</div>		<!-- .leftcol ends -->

		<?php
		return ob_get_clean();
	}
}

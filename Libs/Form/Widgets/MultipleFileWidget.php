<?php
namespace Asgard\Admin\Libs\Form\Widgets;

class MultipleFileWidget extends \Asgard\Form\Widgets\HTMLWidget {
	public function render($options=array()) {
		$options = $this->options+$options;
		
		$attrs = array();
		if(isset($options['attrs']))
			$attrs = $options['attrs'];

		$str = HTMLHelper::tag('input', array(
			'type'	=>	'file',
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		)+$attrs);
		$entity = $this->field->form->getEntity();
		$name = $this->field->name;		
		$optional = !$entity->property($name)->required;
		$path = $entity->$name->get();

		if($entity->isNew())
			return null;
		$uid = Tools::randstr(10);
		HTML::code_js("
			$(function(){
				multiple_upload('$uid', '".$this->field->form->controller->url_for('addFile', array('id' => $entity->id, 'file' => $name), false)."');
			});");
		ob_start();
		?>
		<div class="block">
		
			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				
				<h2><?php echo $name ?></h2>
				<?php
				if(isset($options['nb']))
					echo '<span>'.$options['nb'].'</span>';
				?>
			</div>		<!-- .block_head ends -->
			
			<div class="block_content">
				<script>
				window.parentID = <?php echo $entity->id ?>;
				</script>
				<ul class="imglist">
					<?php
					$i=1;
					foreach($path as $one_path):
					?>
					<li>
						<img src="<?php echo \Asgard\Core\App::get('url')->to('imagecache/admin_thumb/'.$one_path) ?>" alt=""/>
						<ul>
							<li class="view"><a href="<?php echo \Asgard\Core\App::get('url')->to($one_path) ?>" rel="facebox">Voir</a></li>
							<li class="delete"><a href="<?php echo $this->field->form->controller->url_for('deleteFile', array('id' => $entity->id, 'pos' => $i, 'file' => $name), false) ?>">Suppr.</a></li>
						</ul>
					</li>
					<?php
					$i++;
					endforeach;
					?>
					</li>
					
				</ul>
				
				<p id="<?php echo $uid ?>">
					<label><?php echo __('Upload:') ?></label><br />
					<input type="file" id="<?php echo $uid ?>-filesupload" class="filesupload" /><br/>
					<span class="uploadmsg"><?php echo __('Maximum size 3Mb') ?></span>
					<div id="<?php echo $uid ?>-custom-queue"></div>
				</p>
				
			</div>		<!-- .block_content ends -->
			
			<div class="bendl"></div>
			<div class="bendr"></div>
			
		</div>		<!-- .leftcol ends -->

		<?php
		return ob_get_clean();
		/*if($entity->isOld() && $entity->$name && $entity->$name->exists()) {
			$path = $entity->$name->get();
			if(!$path)
				return $str;
			if($entity->property($name)->filetype == 'image') {
				$str .= '<p>
					<a href="../'.$path.'" rel="facebox"><img src="'.\Asgard\Core\App::get('url')->to(ImageCache::src($path, 'admin_thumb')).'" alt=""/></a>
				</p>';
			}
			else {
				$str .= '<p>
					<a href="../'.$path.'">'.__('Download').'</a>
				</p>';
			}
			
			if($optional)
				$str .= '<a href="'.$this->field->form->controller->url_for('deleteSingleFile', array('file'=>$name, 'id'=>$entity->id)).'">'. __('Delete').'</a><br/><br/>';
		}*/

		// return $str;
	}
}

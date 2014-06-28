<?php
namespace Admin\Libs\Form\Widgets;

class GroupWidget extends \Asgard\Form\Widget {
	public $group;

	public function render(array $options=[]) {
		ob_start();
		?>

		<div class="block">
		
			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				
				<h2><?=$this->group->label() ?></h2>
				<?php
				if(isset($options['nb']))
					echo '<span>'.$options['nb'].'</span>';
				?>
			</div>		<!-- .block_head ends -->
			
			<div class="block_content">

		<?php
		$id = \Asgard\Common\Tools::randStr(10);
		$res = "
		<script>
		function add".$id."() {
			var newElement = $('<p class=\"element\">".$this->group->renderTemplate("'+$('.element').length+'")." <input onclick=\"javascript:$(this).parent().remove()\" type=\"button\" value=\"".__('Remove')."\" class=\"submit short\"></p>');
			$('#".$id."').append(newElement);
		}
		</script>
		<div id=\"".$id."\">
		";
		foreach($this->group as $one) {
			$res .= '<p class="element">';
			$res .= $one->def(['label'=>false]).' <input onclick="javascript:$(this).parent().remove()" type="button" value="'.__('Remove').'" class="submit short">';
			$res .= '</p>';
		}
		$res .= '</div>';
		$res .= '<input onclick="javascript:add'.$id.'()" type="button" value="'.__('Add').'" class="submit short">';
		echo $res;
		?>
			</div>		<!-- .block_content ends -->
			
			<div class="bendl"></div>
			<div class="bendr"></div>
			
		</div>		<!-- .leftcol ends -->
		<?php
		return ob_get_clean();
	}
}
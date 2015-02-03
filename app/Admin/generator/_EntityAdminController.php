<%
namespace <?=ucfirst($bundle['namespace']) ?>\Controllers;

/**
 * @Prefix("admin/<?=$entity['meta']['plural'] ?>")
 */
class <?=ucfirst($entity['meta']['name']) ?>AdminController extends \Admin\Libs\Controller\EntityAdminController {
	protected $_entity = '<?=$entity['meta']['entityClass'] ?>';
	protected $_plural = '<?=$entity['meta']['plural'] ?>';

	public function __construct() {
		$this->_messages = [
		<?php foreach($entity['messages'] as $k=>$v): ?>
			'<?=$k ?>'			=>	__('<?=$v ?>'),
		<?php endforeach ?>
		];
		parent::__construct();
	}
	
	public function formConfigure($entity) {
		$form = $this->container->make('adminEntityForm', [$entity, $this]);<?php foreach($entity['relations'] as $relation): ?>
		$form->addRelation('<?php echo $relation ?>');<?php endforeach ?>
		
		return $form;
	}
}
<%
namespace <?php echo ucfirst($bundle['namespace']) ?>\Controllers;

/**
@Prefix('admin/<?php echo $entity['meta']['plural'] ?>')
*/
class <?php echo ucfirst($entity['meta']['name']) ?>AdminController extends \App\Admin\Libs\Controller\EntityAdminController {
	protected $_entity = '<?php echo $entity['meta']['entityClass'] ?>';
	protected $_entities = '<?php echo $entity['meta']['plural'] ?>';

	public function __construct() {
		$this->_messages = array(
		<?php foreach($entity['admin']['messages'] as $k=>$v): ?>
			'<?php echo $k ?>'			=>	__('<?php echo $v ?>'),
		<?php endforeach ?>
		);
		parent::__construct();
	}
	
	public function formConfigure($entity) {
		$form = new \App\Admin\Libs\Form\AdminEntityForm($entity, $this);
		
		return $form;
	}
}
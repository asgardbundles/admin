<?php
namespace Admin\Libs\Controller;

abstract class SubentityAdminController extends EntityAdminController {
	protected $_parent = null;

	public function configure(\Asgard\Http\Request $request) {
		$parentClass = $this->parentEntity;
		$this->parent = $parentClass::load($this->request['parent_id']);
		if(!$this->parent)
			throw new \Asgard\Core\Exception\NotFoundException;
		parent::before($request);
	}
	
	/**
	@Route('')
	*/
	public function indexAction(\Asgard\Http\Request $request) {
		$_entity = $this->_entity;
		$definition = $_entity::getStaticDefinition();
		$_plural = $this->_plural;
		
		$this->searchForm = new \Asgard\Form\Form(null, ['method'=>'get']);
		$this->searchForm->search = new \Asgard\Form\Field\TextField;
	
		//submitted
		$controller = $this;
		$this->globalactions = [];
		$definition->trigger('asgardadmin_globalactions', [&$this->globalactions], function($chain, &$actions) use($_entity, $controller) {
			$actions[] = [
				'text'	=>	__('Delete'),
				'value'	=>	'delete',
				'callback'	=>	function() use($_entity, $controller) {
					$i = 0;
					if(\Asgard\Container\Container::get('post')->size()>1) {
						foreach(POST::get('id') as $id)
							$i += $_entity::destroyOne($id);
					
						Flash::addSuccess(sprintf($controller->_messages['many_deleted'], $i));
					}
				}
			];
		});
		foreach($this->globalactions as $action) {
			if(\Asgard\Container\Container::get('post')->get('action') == $action['value']) {
				$cb = $action['callback'];
				$cb();
			}
		}
		
		$conditions = [];
		#Search
		if(\Asgard\Container\Container::get('get')->get('search')) {
			$conditions['or'] = [];
			foreach($_entity::propertyNames() as $property) {
				if($property != 'id')
					$conditions['or']["`$property` LIKE ?"] = '%'.\Asgard\Container\Container::get('get')->get('search').'%';
			}
		}
		#Filters
		elseif(\Asgard\Container\Container::get('get')->get('filter')) {
			$conditions['and'] = [];
			foreach(\Asgard\Container\Container::get('get')->get('filter') as $key=>$value) {
				if($value)
					$conditions['and']["`$key` LIKE ?"] = '%'.$value.'%';
			}
		}

		// $pagination = $_entity::where($conditions);
		$parent = $this->parent;
		$pagination = $parent->::where($conditions);
		#define parent through relation then...
		
		if(isset($this->_orderby))
			$pagination->orderBy($this->_orderby);

		$this->orm = $pagination;

		$definition->trigger('asgardadmin_index', [$this]);

		$this->orm->paginate(
			\Asgard\Container\Container::get('get')->get('page', 1),
			10
		);
		$this->$_plural = $this->orm->get();
		$this->paginator = $this->orm->getPaginator();
	}
}

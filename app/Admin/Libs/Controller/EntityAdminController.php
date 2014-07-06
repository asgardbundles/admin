<?php
namespace Admin\Libs\Controller;

abstract class EntityAdminController extends AdminParentController {
	protected $_entity = null;
	protected $_singular = null;
	protected $_plural = null;
	protected static $_hooks = [];
	
	public function __construct() {
		$this->__messages = [
			'modified'			=>	__('Element updated with success.'),
			'created'				=>	__('Element created with success.'),
			'many_deleted'	=>	__('%s elements deleted.'),
			'deleted'				=>	__('Element deleted with success.'),
		];

		$_entity = $this->_entity;

		if($this->_singular === null)
			$this->_singular = strtolower($_entity::getShortName());
		if($this->_plural === null)
			$this->_plural = $this->_singular.'s';
		if(isset($this->_messages))
			$this->_messages = array_merge($this->__messages, $this->_messages);
		else
			$this->_messages = $this->__messages;
	}
	
	public function getEntity() {
		return $this->_entity;
	}
	
	public static function getIndexURL() {
		return static::url_for('index');
	}
	
	public static function getEditURL($id) {
		return static::url_for('edit', ['id'=>$id]);
	}

	protected function getLocalesLinks($entity) {
		if(!$entity->isI18N() || !$this->app['config']['locales'])
			return;
		$links = [];
		if($this->request['locale'])
			$current = $this->request['locale'];
		else
			$current = $this->app['config']['locale'];
		foreach($this->app['config']['locales'] as $locale) {
			if($locale === $current)
				$links[] = '<a href="'.$this->url_for('editLocale', ['id'=>$this->request['id'], 'locale'=>$locale]).'"><b>'.strtoupper($locale).'</b></a>';
			else
				$links[] = '<a href="'.$this->url_for('editLocale', ['id'=>$this->request['id'], 'locale'=>$locale]).'">'.strtoupper($locale).'</a>';
		}
		return implode(' | ', $links);
	}
	
	/**
	@Route(":id/edit/:locale")
	*/
	public function editLocaleAction(\Asgard\Http\Request $request) {
		$this->view = 'form';
		return $this->editAction($request);
	}
	
	/**
	@Route("")
	*/
	public function indexAction(\Asgard\Http\Request $request) {
		$_entity = $this->_entity;
		$definition = $_entity::getDefinition();
		$_plural = $this->_plural;
	
		#submitted
		$controller = $this;
		$this->globalactions = [];
		$this->app['hooks']->trigger('asgardadmin_globalactions', [$controller, &$this->globalactions], function($chain, $controller, &$actions) {
			$_entity = $controller->getEntity();
			$actions['delete'] = [
				'text'	=>	__('Delete'),
				'callback'	=>	function($entityClass, $controller) {
					$i = 0;
					if($controller->request->post->size() > 1) {
						foreach($controller->request->post->get('id') as $id)
							$i += $entityClass::destroyOne($id);
					
						$this->getFlash()->addSuccess(sprintf($controller->_messages['many_deleted'], $i));
					}
				}
			];
		});
		if(isset($this->globalactions[$this->request->post->get('action')]))
			$this->globalactions[$this->request->post->get('action')]['callback']($_entity, $this);
		
		#Search
		$this->searchForm = $this->app->make('form', ['search', ['method'=>'get'], $request]);
		$this->searchForm['search'] = new \Asgard\Form\Fields\TextField;

		$conditions = [];
		if($this->searchForm->sent() && $term=$this->searchForm['search']->value()) {
			$conditions['or'] = [];
			foreach($_entity::propertyNames() as $property) {
				if($property !== 'id')
					$conditions['or'][$property.' LIKE ?'] = '%'.$term.'%';
			}
		}

		$pagination = $_entity::where($conditions);
		
		if(isset($this->_orderby))
			$pagination->orderBy($this->_orderby);

		$this->orm = $pagination;

		$definition->trigger('asgardadmin_index', [$this]);

		$this->orm->paginate(
			$request->get->get('page', 1),
			10
		);
		$this->$_plural = $this->orm->get();
		$this->paginator = $this->orm->getPaginator();
	}
	
	/**
	@Route(":id/edit")
	*/
	public function editAction(\Asgard\Http\Request $request) {
		$_singular = $this->_singular;
		$_entity = $this->_entity;
		
		if(!($this->{$_singular} = $_entity::load($request['id'])))
			throw new \Asgard\Core\Exceptions\NotFoundException;

		$this->form = $this->formConfigure($this->{$_singular});
		if($request['locale']) {
			$this->{$_singular}->setLocale($request['locale']);
			$this->form->setParam('action', $this->url_for('editLocale', ['id'=>$request['id'], 'locale'=>$request['locale']]));
		}
		
		$this->original = clone $this->{$_singular};
	
		if($this->form->sent()) {
			try {
				$this->form->save();
				$this->getFlash()->addSuccess($this->_messages['modified']);
				if($request->post->has('send'))
					return $request->server['HTTP_REFERER'] !== $request->url->full()
						?
						$this->response->back()
						:$this->response->redirect($this->url_for('index'));
			} catch(\Asgard\Form\FormException $e) {
				$this->getFlash()->addError(__('There was at least one error.'));
				$this->getFlash()->addError($this->form->getGeneralErrors());
				$this->response->setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			$this->getFlash()->addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			$this->response->setCode(400);
		}
		
		$this->view = 'form';
	}
	
	/**
	@Route("new")
	*/
	public function newAction(\Asgard\Http\Request $request) {
		$_singular = $this->_singular;
		$_entity = $this->_entity;
		
		$this->{$_singular} = new $_entity;
		$this->original = clone $this->{$_singular};
	
		$this->form = $this->formConfigure($this->{$_singular});
	
		if($this->form->sent()) {
			try {
				$this->form->save();
				$this->getFlash()->addSuccess($this->_messages['created']);
				if($request->post->has('send'))
					return $request->server->get('HTTP_REFERER') !== $request->url->full()
						? $this->response->back()
						:$this->response->redirect($this->url_for('index'));
				else
					return $this->response->redirect($this->url_for('edit', ['id'=>$this->{$_singular}->id]));
			} catch(\Asgard\Form\FormException $e) {
				$this->getFlash()->addError($this->form->getGeneralErrors());
				$this->response->setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			$this->getFlash()->addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			$this->response->setCode(400);
		}
		
		$this->view = 'form';
	}
	
	/**
	@Route(":id/delete")
	*/
	public function deleteAction(\Asgard\Http\Request $request) {
		$_entity = $this->_entity;
		
		!$_entity::destroyOne($request['id']) ?
			$this->getFlash()->addError($this->_messages['unexisting']) :
			$this->getFlash()->addSuccess($this->_messages['deleted']);
			
		return $this->response->redirect($this->url_for('index'));
	}
}

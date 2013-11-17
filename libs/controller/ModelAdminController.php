<?php
namespace App\Admin\Libs\Controller;

abstract class ModelAdminController extends AdminParentController {
	protected static $_model = null;
	protected static $_singular = null;
	protected static $_plural = null;
	protected static $_hooks = array();
	
	function __construct() {
		$this->__messages = array(
			'modified'			=>	__('Element updated with success.'),
			'created'				=>	__('Element created with success.'),
			'many_deleted'	=>	__('%s elements deleted.'),
			'deleted'				=>	__('Element deleted with success.'),
		);

		\Hook::trigger('coxisadmin', get_called_class());

		if(static::$_singular === null)
			static::$_singular = strtolower(\Coxis\Utils\NamespaceUtils::basename(static::$_model));
		if(static::$_plural === null)
			static::$_plural = static::$_singular.'s';
		if(isset($this->_messages))
			$this->_messages = array_merge($this->__messages, $this->_messages);
		else
			$this->_messages = $this->__messages;
	}
	
	public static function getModel() {
		return static::$_model;
	}
	
	public static function getIndexURL() {
		return static::url_for('index');
	}
	
	public static function getEditURL($id) {
		return static::url_for('edit', array('id'=>$id));
	}
	
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$_model = static::$_model;
		$_plural = static::$_plural;
		
		$this->searchForm = new \Coxis\Form\Form(array('method'=>'get'));
		$this->searchForm->search = new \Coxis\Form\Fields\TextField;
	
		//submitted
		$controller = $this;
		$this->globalactions = array();
		\Hook::trigger('coxis_'.static::$_model.'_globalactions', array(&$this->globalactions), function($chain, &$actions) use($_model, $controller) {
			$actions[] = array(
				'text'	=>	__('Delete'),
				'value'	=>	'delete',
				'callback'	=>	function() use($_model, $controller) {
					$i = 0;
					if(POST::size()>1) {
						foreach(POST::get('id') as $id)
							$i += $_model::destroyOne($id);
					
						Flash::addSuccess(sprintf($controller->_messages['many_deleted'], $i));
					}
				}
			);
		});
		foreach($this->globalactions as $action) {
			if(POST::get('action') == $action['value']) {
				$cb = $action['callback'];
				$cb();
			}
		}
		
		$conditions = array();
		#Search
		if(GET::get('search')) {
			$conditions['or'] = array();
			foreach($_model::propertyNames() as $property) {
				if($property != 'id')
					$conditions['or']["`$property` LIKE ?"] = '%'.GET::get('search').'%';
			}
		}
		#Filters
		elseif(GET::get('filter')) {
			$conditions['and'] = array();
			foreach(GET::get('filter') as $key=>$value) {
				if($value)
					$conditions['and']["`$key` LIKE ?"] = '%'.$value.'%';
			}
		}

		$pagination = $_model::where($conditions);
		
		if(isset(static::$_orderby))
			$pagination->orderBy(static::$_orderby);

		$this->orm = $pagination;

		\Hook::trigger('coxisadmin_'.static::$_model.'_index', array($this));

		$this->paginator = null;

		$this->$_plural = $this->orm->paginate(
			GET::get('page', 1),
			10,
			$this->paginator
		);
	}
	
	/**
	@Route(':id/edit')
	*/
	public function editAction($request) {
		$_singular = static::$_singular;
		$_model = static::$_model;
		
		if(!($this->{$_singular} = $_model::load($request['id'])))
			throw new NotFoundException;
		$this->original = clone $this->{$_singular};

		$this->form = $this->formConfigure($this->{$_singular});
	
		if($this->form->isSent()) {
			try {
				$this->form->save();
				\Flash::addSuccess($this->_messages['modified']);
				if(\POST::has('send'))
					return Server::has('HTTP_REFERER') && Server::get('HTTP_REFERER') !== \URL::full() ? \Response::back():\Response::redirect($this->url_for('index'));
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($this->form->getGeneralErrors());
				\Response::setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			\Flash::addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			\Response::setCode(400);
		}
		
		$this->setRelativeView('form.php');
	}
	
	/**
	@Route('new')
	*/
	public function newAction($request) {
		$_singular = static::$_singular;
		$_model = static::$_model;
		
		$this->{$_singular} = new $_model;
		$this->original = clone $this->{$_singular};
	
		$this->form = $this->formConfigure($this->{$_singular});
	
		if($this->form->isSent()) {
			try {
				$this->form->save();
				\Flash::addSuccess($this->_messages['created']);
				if(\POST::has('send'))
					return Server::has('HTTP_REFERER') && Server::get('HTTP_REFERER') !== \URL::full() ? \Response::back():\Response::redirect($this->url_for('index'));
				else
					return \Response::redirect($this->url_for('edit', array('id'=>$this->{$_singular}->id)));
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($this->form->getGeneralErrors());
				\Response::setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			\Flash::addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			\Response::setCode(400);
		}
		
		$this->setRelativeView('form.php');
	}
	
	/**
	@Route(':id/delete')
	*/
	public function deleteAction($request) {
		$_model = static::$_model;
		
		!$_model::destroyOne($request['id']) ?
			\Flash::addError($this->_messages['unexisting']) :
			\Flash::addSuccess($this->_messages['deleted']);
			
		return \Response::redirect($this->url_for('index'));
	}
	
	/**
	@Route(':id/deletefile/:file')
	*/
	public function deleteSingleFileAction($request) {
		$_model = static::$_model;
		$_singular = static::$_singular;
		
		if(!($this->{$_singular} = $_model::load($request['id'])))
			$this->forward404();
			
		$file = $request['file'];
		$this->{$_singular}->$file->delete();
		\Flash::addSuccess(__('File deleted with success.'));
		return \Response::back();
	}
	
	/**
	@Route(':id/:file/add')
	*/
	public function addFileAction($request) {
		Memory::set('layout', false);
		$_model = static::$_model;;
		if(!($model = $_model::load($request['id'])))
			$this->forward404();
		if(!$model->hasProperty($request['file']))
			$this->forward404();
			
		if(\File::has('Filedata')) {
			$file = \File::get('Filedata');
			$files = array($request['file'] => array('name'=>$file['name'], 'path'=>$file['tmp_name']));
		}
		else
			return \Response::setCode(500)->setContent(__('An error occured.'));

		$file = $request['file'];
		$model->$file->add($files);
		$model->save(array(), true);
		$final_paths = $model->$file->get();
		$response = array(
			'url' => array_pop($final_paths),
			'deleteurl' => $this->url_for('deleteFile', array('id' => $model->id, 'pos' => sizeof($final_paths)+1, 'file' => $request['file'])),
		);
		return \Response::setCode(200)->setContent(json_encode($response));
	}
	
	/**
	@Route(':id/:file/delete/:pos')
	*/
	public function deleteFileAction($request) {
		$_model = static::$_model;
		if(!($model = $_model::load($request['id'])))
			$this->forward404();
		if(!$model->hasProperty($request['file']))
			$this->forward404();
		
		$file = $request['file'];
			
		$paths = $model->$file->get();

		if(!isset($paths[$request['pos']-1]))
			return \Response::redirect($this->url_for('edit', array('id' => $model->id)), false)->setCode(404);
		
		try {
			$model->$file->delete($request['pos']-1);
			$model->save(null, true);
			\Flash::addSuccess(__('File deleted with success.'));
		} catch(\Exception $e) {
			\Flash::addError(__('There was an error in the file'));
		}
		
		try {
			return \Response::redirect($this->url_for('edit', array('id' => $model->id)), false);
		} catch(\Exception $e) {
			return \Response::redirect($this->url_for('index'), false);
		}
	}
	
	public static function addHook($hook) {
		static::$_hooks[] = $hook;
		
		$hook['route'] = str_replace(':route', $hook['route'], \Router::getRouteFor(array(get_called_class(), 'hooks')));
		$hook['controller'] = get_called_class();
		$hook['action'] = 'hooks';
		\Router::addRoute($hook);
	}
	
	/**
	@Route(value = 'hooks/:route', requirements = {
		route = {
			type = 'regex',
			regex = '.+'
		}	
	})
	*/
	public function hooksAction($request) {
		$_model = static::$_model;

		$controller = get_called_class();

		foreach(static::$_hooks as $hook) {
			if($results = \Router::matchWith($hook['route'], $request['route'])) {
				$newRequest = new \Coxis\Core\Request;
				$newRequest->parentController = $controller;
				$newRequest->params = array_merge($request->params, $results);
				return Controller::run($hook['controller'], $hook['action'], $newRequest);
			}
		}
		throw new NotFoundException('Page not found');
	}
}

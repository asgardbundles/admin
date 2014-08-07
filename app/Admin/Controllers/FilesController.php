<?php
namespace Admin\Controllers;

/**
 * @Prefix("admin/files/:entityAlias/:id/:file")
 */
class FilesController extends \Admin\Libs\Controller\AdminParentController {
	public function before(\Asgard\Http\Request $request) {
		$this->layout = false;
		$entityAlias = $request['entityAlias'];
		$entityClass = $this->container['adminManager']->getClass($entityAlias);
		if(!($this->entity = $entityClass::load($request['id'])))
			$this->forward404();
		if(!$this->entity->hasProperty($request['file']))
			$this->forward404();

		return parent::before($request);
	}

	/**
	 * @Route("add")
	 */
	public function addAction($request) {
		$entity = $this->entity;
			
		if(!$request->file->has('Filedata'))
			return $this->response->setCode(400)->setContent(__('An error occured.'));

		try {
			$postFile = $request->file['Filedata'];
			$postFile = \Asgard\Form\HttpFile::createFromArray($postFile);

			$fileName = $request['file'];

			if($entity->property($fileName) instanceof \Asgard\Entity\Properties\ImageProperty)
				$type = 'image';
			else
				$type = 'file';


			$postFile = $entity->get($fileName)->add($postFile);
			$entity->save();

			$url = $postFile->url();

			$response = [
				'type' => $type,
				'url' => $url,
				'deleteurl' => $this->url_for('deleteOne', ['entityAlias' => $request['entityAlias'], 'id' => $entity->id, 'pos' => $entity->get($fileName)->size(), 'file' => $request['file']]),
			];
			if($entity->property($fileName) instanceof \Asgard\Entity\Properties\ImageProperty)
				$response['thumb_url'] = $request->url->to('imagecache/admin_thumb/'.$postFile->relativeToWebDir());
		} catch(\Asgard\Orm\EntityException $e) {
			return $this->response->setCode(400)->setContent(__('An error occured.'));
		}

		return $this->response->setCode(200)->setContent(json_encode($response));
	}

	/**
	 * @Route("delete")
	 */
	public function deleteAction($request) {
		$entity = $this->entity;
		$file = $request['file'];
		$entity->$file->toDelete();
		$entity->save();
		$this->getFlash()->addSuccess(__('File deleted with success.'));

		return $this->back();
	}

	/**
	 * @Route("delete/:pos")
	 */
	public function deleteOneAction($request) {
		$entity = $this->entity;
		$fileName = $request['file'];

		try {
			$files = $entity->get($fileName);
			if(isset($files[$request['pos']-1])) {		
				$files[$request['pos']-1]->toDelete();
				$entity->save();

				$this->getFlash()->addSuccess(__('File deleted with success.'));

				return $this->back();
			}
		} catch(\Exception $e) {throw $e;}
		return $this->response->setCode(400)->setContent(__('An error occured.'));
	}
}
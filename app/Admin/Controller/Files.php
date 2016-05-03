<?php
namespace Admin\Controller;

/**
 * @Prefix("admin/files/:entityAlias/:id/:file")
 */
class Files extends \Admin\Libs\Controller\AdminParentController {
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

			$fileName = $request['file'];
			$property = $entity->property($fileName);

			$postFile = $entity->get($fileName)->add($postFile);
			$entity->save();

			$deleteurl = $this->url_for('deleteOne', ['entityAlias' => $request['entityAlias'], 'id' => $entity->id, 'pos' => $entity->get($fileName)->size(), 'file' => $request['file']]);
			$downloadurl = $this->url_for('downloadOne', ['entityAlias' => $request['entityAlias'], 'id' => $entity->id, 'pos' => $entity->get($fileName)->size(), 'file' => $request['file']]);
			$response = '<li><a href="'.$downloadurl.'">'.__('Download').'</a> | <a href="'.$deleteurl.'">'.__('Delete').'</a></li>';
		} catch(\Asgard\Orm\EntityException $e) {
			return $this->response->setCode(400)->setContent(__('An error occured.'));
		}

		return $this->response->setCode(200)->setContent($response);
	}

	/**
	 * @Route("download")
	 */
	public function downloadAction($request) {
		$entity = $this->entity;
		$file = $request['file'];
		$path = $entity->$file->src();
		if(!file_exists($path))
			return $this->response->setCode(404);

	    $this->response->setHeader('Content-Description', 'File Transfer');
	    $this->response->setHeader('Content-Type', 'application/octet-stream');
	    $this->response->setHeader('Content-Disposition', 'attachment; filename='.basename($path));
	    $this->response->setHeader('Expires', '0');
	    $this->response->setHeader('Cache-Control', 'must-revalidate');
	    $this->response->setHeader('Pragma', 'public');
	    $this->response->setHeader('Content-Length', '' . filesize($path));
	    readfile($path);
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
	 * @Route("download/:pos")
	 */
	public function downloadOneAction($request) {
		$entity = $this->entity;
		$fileName = $request['file'];

		$files = $entity->get($fileName);
		if(!isset($files[$request['pos']-1]))
			return $this->response->setCode(404);

		$file = $files[$request['pos']-1];
		$path = $file->src();
		if(!file_exists($path))
			return $this->response->setCode(404);

	    $this->response->setHeader('Content-Description', 'File Transfer');
	    $this->response->setHeader('Content-Type', 'application/octet-stream');
	    $this->response->setHeader('Content-Disposition', 'attachment; filename='.basename($path));
	    $this->response->setHeader('Expires', '0');
	    $this->response->setHeader('Cache-Control', 'must-revalidate');
	    $this->response->setHeader('Pragma', 'public');
	    $this->response->setHeader('Content-Length', '' . filesize($path));
	    readfile($path);
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
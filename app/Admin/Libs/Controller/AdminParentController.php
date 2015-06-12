<?php
namespace Admin\Libs\Controller;

abstract class AdminParentController extends \Asgard\Http\Controller {
	public function before(\Asgard\Http\Request $request) {
		$this->set('layout', [$this, 'layout']);
		$this->set('htmlLayout', false);
		if(!$this->container['adminAuth']->isConnected()) {
			$request->session['redirect_to'] = $request->url->full();
			return $this->response->setCode(401)->redirect('admin/login');
		}
	}

	public function layout($controller, $content) {
		return \Asgard\Templating\PHPTemplate::renderFile(dirname(dirname(__DIR__)).'/html/admin/layout.php', ['content'=>$content, 'controller'=>$this]);
	}
}

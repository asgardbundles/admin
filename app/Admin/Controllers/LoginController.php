<?php
namespace Admin\Controllers;

class LoginController extends \Asgard\Http\Controller {
	public function before(\Asgard\Http\Request $request) {
		$this->layout = false;
		$this->htmlLayout = false;
	}
	
	/**
	 * @Route("admin/login")
	 */
	public function loginAction(\Asgard\Http\Request $request) {
		$auth = $this->container['adminAuth'];
		$username = $request->post['username'];
		$password = $request->post['password'];

		if($auth->isConnected())
			return $this->response->redirect('admin');

		if($username !== null && $password !== null) {
			if($administrator = $auth->attempt($username, $password)) {
				if($request->post['remember'] == 'yes')
					$auth->remember($username, $password);
				if($request->session->has('redirect_to'))
					return $this->response->redirect($request->session->get('redirect_to'), false);
				else
					return $this->response->redirect('admin');
			}
			elseif($request->post->has('username')) {
				$this->response->setCode(400);
				$error = __(__('Invalid username or password.'));
			}
		}
	}
	
	/**
	 * @Route("admin/logout")
	 */
	public function logoutAction(\Asgard\Http\Request $request) {
		$this->container['adminAuth']->disconnect();

		return $this->response->redirect('');
	}
	
	/**
	 * @Route("admin/forgotten")
	 */
	public function forgottenAction($request) {
		$this->form = $this->container->make('form', ['forgotten', [], $this->request]);
		$this->form['username'] = new \Asgard\Form\Fields\TextField(['required'=>true]);

		$error = null;
		if($request['code']) {
			$hash = $request['code'];
			$admin = \Admin\Entities\Administrator::where(['SHA1(CONCAT(username, \'-\', password))' => $hash])->first();
			if(!$admin)
				$error = __('Invalid code.');
			else {
				$password = \Asgard\Common\Tools::randStr(10);
				$admin->save(['password'=>$password]);
				$data = $this->container['data'];
				$this->container->make('email')->send(function($msg) use($password, $admin, $data) {
					$msg->to($admin->email);
					$msg->from($data['email']);
					$msg->html(__('Your new password is: ').$password);
				});
				$this->getFlash()->addSuccess(__('An email with your new password was sent to your email address.'));
			}
		}
		elseif($this->form->sent()) {
			if($this->form->isValid()) {
				$user = $this->form['username']->value();
				if($admin = \Admin\Entities\Administrator::loadBy('username', $user)) {
					if($admin->email) {
						$link = $this->url_for('confirm', ['code'=>sha1($admin->email.'-'.$admin->password)]);
						$data = $this->container['data'];
						$this->container->make('email')->send(function($msg) use($link, $admin, $data) {
							$msg->to($admin->email);
							$msg->from($data['email']);
							$msg->html(__('Please click on the following link to get a new password: ').$link);
						});
						$this->getFlash()->addSuccess('An email was sent to your email address.');
					}
					else
						$error = __('This administrator does not have a valid email address. Please ask the main administrator.');
				}
				else
					$error = __('This username does not exist.');
			}
			else
				$error = __('Please fill in your username.');
		}
		if($error) {
			$this->response->setCode(400);
			$this->getFlash()->addError($error);
		}
	}
	
	/**
	 * @Route("admin/forgotten/:code")
	 */
	public function confirmAction($request) {
		$this->setRelativeView('forgotten.php');
		return $this->forgottenAction($request);
	}
}
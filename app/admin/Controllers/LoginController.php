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
		$auth = $this->app['adminAuth'];
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
			elseif($request->post->has('username'))
				$this->getFlash()->addError(__('Invalid username or password.'));
		}
	}
	
	/**
	 * @Route("admin/logout")
	 */
	public function logoutAction(\Asgard\Http\Request $request) {
		$this->app['adminAuth']->disconnect();

		return $this->response->redirect('');
	}
	
	/**
	 * @Route("admin/forgotten")
	 */
	public function forgottenAction($request) {
		$this->form = $this->app->make('form', ['forgotten', [], [], $this->request]);
		$this->form['username'] = new \Asgard\Form\Fields\TextField(['required'=>true]);

		if($request['code']) {
			$hash = $request['code'];
			$admin = \Admin\Entities\Administrator::where(['SHA1(CONCAT(username, \'-\', password))' => $hash])->first();
			if(!$admin)
				$this->getFlash()->addError('Invalid code.');
			else {
				$password = \Asgard\Common\Tools::randStr(10);
				$admin->save(['password'=>$password]);
				$data = $this->app['data'];
				$this->app->make('email')->send(function($msg) use($password, $admin, $data) {
					$msg->to($admin->email);
					$msg->from($data['email']);
					$msg->html(__('Your new password is: ').$password);
				});
				$this->getFlash()->addSuccess(__('An email with your new password was sent to your email address.'));
			}
		}
		elseif($this->form->isSent()) {
			if($this->form->isValid()) {
				$user = $this->form['username']->getValue();
				if($admin = \Admin\Entities\Administrator::loadBy('username', $user)) {
					if($admin->email) {
						$link = $this->url_for('confirm', ['code'=>sha1($admin->email.'-'.$admin->password)]);
						$data = $this->app['data'];
						$this->app->make('email')->send(function($msg) use($link, $admin, $data) {
							$msg->to($admin->email);
							$msg->from($data['email']);
							$msg->html(__('Please click on the following link to get a new password: ').$link);
						});
						$this->getFlash()->addSuccess('An email was sent to your email address.');
					}
					else
						$this->getFlash()->addError('This administrator does not have a valid email address. Please ask the main administrator.');
				}
				else
					$this->getFlash()->addError('This username does not exist.');
			}
			else
				$this->getFlash()->addError('Please fill in your useraname.');
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
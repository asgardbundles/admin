<?php
namespace Admin\Hook;

class NotfoundHooks extends \Asgard\Hook\HookContainer {
	/**
	 * @Hook("Asgard.Http.Start")
	 */
	public static function start($chain, $request) {
		if(preg_match('/^admin/', $request->url->get())) {
			$chain->getContainer()['hooks']->preHook('Asgard.Http.Exception.Asgard\Http\Exception\NotFoundException', function($chain, $e, &$response, $request) {
				$response = $chain->getContainer()['httpKernel']->runController('Admin\Controller\DefaultAdmin', '_404', $request)->setCode(404);
				$chain->stop();
			});
		}
	}
}
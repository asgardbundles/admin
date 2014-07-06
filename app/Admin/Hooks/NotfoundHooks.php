<?php
namespace Admin\Hooks;

class NotfoundHooks extends \Asgard\Hook\HooksContainer {
	/**
	 * @Hook("Asgard.Http.Start")
	 */
	public static function start($chain, $request) {
		if(preg_match('/^admin/', $request->url->get())) {
			$chain->app['hooks']->hookBefore('Asgard.Http.Exception.Asgard\Http\Exceptions\NotFoundException', function($chain, $e, &$response, $request) {
				$response = $chain->app['httpKernel']->runController('Admin\Controllers\DefaultAdminController', '_404', $request)->setCode(404);
				$chain->stop();
			});
		}
	}
}
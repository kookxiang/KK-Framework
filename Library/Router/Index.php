<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace Router;
use Module\User;
use System\Module\Router;
use System\Module\Template;

class Index extends Router {
	public function Index(){
		Router::forceHttp();
		include Template::load('Index');
	}
}

<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('login/', 'Homepage:login');
		$router[] = new Route('moderating/<action>', 'Core:Moderating:records');
		$router[] = new Route('content/<action>', 'Core:Content:program');
		$router[] = new Route('settings/<action>', 'Core:Settings:changePassword');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Core:Homepage:default');
		return $router;
	}

}

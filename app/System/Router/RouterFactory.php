<?php declare(strict_types = 1);

namespace App\System\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{

	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList();

		$router->addRoute('[<locale=cs cs|en>/]<module>[[/<presenter>]/<action>[/<id>]]', [
			'module' => 'System',
			'presenter' => 'Default',
			'action' => 'default',
		]);

		return $router;
	}

}

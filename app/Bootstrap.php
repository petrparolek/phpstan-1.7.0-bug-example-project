<?php declare(strict_types = 1);

namespace App;

use Nette\Configurator;

class Bootstrap
{

	public static function boot(): Configurator
	{
		setlocale(LC_ALL, 'cs_CZ.utf8');

		$configurator = new Configurator();

		$appDir = __DIR__;
		$wwwDir = __DIR__ . '/../www';

		$configurator->setDebugMode(true);

		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		@mkdir($appDir . '/../temp/sessions');

		$configurator->createRobotLoader()
			->addDirectory($appDir)
			->register();

		$configurator->addConfig($appDir . '/config/config.neon');
		$configurator->addConfig($appDir . '/config/config.parameters.local.neon');

		if (file_exists($appDir . '/config/config.local.neon')) {
			$configurator->addConfig($appDir . '/config/config.local.neon');
		}

		$configurator->addConfig($appDir . '/config/config.forms.neon');
		$configurator->addParameters(
			[
				'wwwDir' => $wwwDir,
			]
		);

		return $configurator;
	}

}

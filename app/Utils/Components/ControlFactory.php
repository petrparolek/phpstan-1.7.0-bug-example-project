<?php declare(strict_types = 1);

namespace App\Utils\Components;

use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent as ComponentInterface;
use Nette\DI\Container;
use Nette\UnexpectedValueException;

final class ControlFactory
{

	public function __construct(protected Container $container)
	{
	}

	/**
	 * @param string[] $args
	 */
	public function create(ComponentInterface $parentComponent, string $name, array $args = []): ComponentInterface
	{
		$component = null;

		$ucname = ucfirst($name);

		if ($ucname !== $name) {
			/** @var Control $parent */
			$parent = $parentComponent;
			/** @var string $presenter */
			$presenter = $parent->getPresenter()->getName();
			$modulesArr = explode(':', trim($presenter, ':'));
			array_pop($modulesArr);

			$classPossibilities = ['App\\Components\\' . $ucname];
			$prev = '';

			//auto registration of controls
			foreach ($modulesArr as $module) {
				$prev .= '\\' . $module . 'Module';

				$classPossibilities[] = 'App' . $prev . '\Components\\' . $ucname;
			}

			//manual registration of registered controls as service implemented by App\Components\IForm
			$services = $this->container->findByType(AppComponentInterface::class);

			foreach ($services as $serviceName) {
				$service = $this->container->getService($serviceName);

				$classPossibilities[] = $service::class;
			}

			$classPossibilities = array_unique($classPossibilities);

			/** @var array<string> $classPossibilities */
			$classPossibilities = array_reverse($classPossibilities);

			foreach ($classPossibilities as $class) {
				/** @var bool $pos */
				$pos = strpos($class, $ucname);

				if ($pos) {
					if (class_exists($class)) {
						$component = $this->container->createInstance($class, $args);
						$this->container->callInjects($component);
					}
				}
			}
		}

		if (!$component instanceof ComponentInterface) {
			throw new UnexpectedValueException(
				'Automatic creation component did not return or create the desired component. Component name: ' . $name
			);
		}

		return $component;
	}

}

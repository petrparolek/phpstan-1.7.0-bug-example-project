<?php declare(strict_types = 1);

namespace App\Utils\Components\Bridges;

use App\Utils\Components\ControlFactory;
use Nette\ComponentModel\ComponentInterface;
use Nette\DI\Attributes\Inject;

trait BasePresenterTrait
{

	#[Inject]
	public ControlFactory $controlFactory;

	protected function createComponent(string $name): ?ComponentInterface
	{
		$component = parent::createComponent($name);

		// pokud se nepodari najit tovarnicku na komponentu, zkusime autoload dle jmena
		if (!$component instanceof ComponentInterface) {
			$component = $this->controlFactory->create($this, $name);
		}

		return $component;
	}

}

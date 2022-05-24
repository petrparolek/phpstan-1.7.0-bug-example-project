<?php declare(strict_types = 1);

namespace App\System\Components\Datagrids;

use App\Utils\Components\AppComponentInterface;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * @property Template $template
 */
abstract class BaseDataGrid extends Control implements AppComponentInterface
{

	/**
	 * @param array<mixed> $params
	 */
	public function render(array $params = []): void
	{
		$this->template->setFile(__DIR__ . '/dataGrid.latte')
			->render();
	}

}

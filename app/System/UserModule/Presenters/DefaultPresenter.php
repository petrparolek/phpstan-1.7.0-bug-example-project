<?php declare(strict_types = 1);

namespace App\System\UserModule\Presenters;

use App\System\Presenters\BaseAuthPresenter;
use Nette\Application\ForbiddenRequestException;

final class DefaultPresenter extends BaseAuthPresenter
{

	/**
	 * @param string[] $tabControls
	 * @param string[] $tabPanels
	 */
	public function __construct(public array $tabControls = [], public array $tabPanels = [])
	{
		parent::__construct();
	}

	public function beforeRender(): void
	{
		parent::beforeRender();

		if (!$this->getUser()->isInRole('admin')) {
			throw new ForbiddenRequestException();
		}
	}

	public function renderAdd(): void
	{
		$this->template->tabPanels = $this->tabPanels['addUserForm'];

		$this->template->tabControls = $this->tabControls['addUserForm'];
	}

	public function renderEdit(int $id): void
	{
		$this->template->tabPanels = $this->tabPanels['editUserForm'];

		$this->template->tabControls = $this->tabControls['editUserForm'];
	}

}

<?php declare(strict_types = 1);

namespace App\System\Presenters;

use App\Utils\Components\Bridges\BasePresenterTrait;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Presenter;
use Nette\Security\UserStorageInterface;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{

	use BasePresenterTrait;

	#[Persistent]
	public string $locale = 'cs';

	/**
	 * Stores current request to session.
	 *
	 * @param mixed $expiration optional expiration time
	 */
	public function storeMyRequest(mixed $expiration = '+ 10 minutes', mixed $key = ''): string
	{
		$session = $this->getSession('Nette.Application/requests');

		$session[$key] = [$this->getUser()->isLoggedIn() ? (int) $this->getUser()->getId() : null, $this->getRequest()];
		$session->setExpiration($expiration, $key);

		return $key;
	}

	public function beforeRender(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getUser()->getLogoutReason() === UserStorageInterface::LOGOUT_INACTIVITY) {
				$this->flashMessage('Byl jste odhlášen z důvodu neaktivity', 'warning');
			}
		}

		if ($this->isAjax() && (bool) $this->getParameter('isModal')) {
			$this->payload->showModal = true;
			$this->payload->modalId = 'myModal';
			$this->redrawControl('modal');
		}

		$this->setLayout(__DIR__ . '/templates/@layout.latte');
	}

}

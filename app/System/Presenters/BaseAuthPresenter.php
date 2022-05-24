<?php declare(strict_types = 1);

namespace App\System\Presenters;

use App\System\Model\EntityManagerDecorator;
use App\System\Repositories\UserRepository;
use ArrayIterator;
use DateTime;
use Nette\DI\Attributes\Inject;
use Nette\Http\SessionSection;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;

/**
 * Base presenter for all application presenters.
 */
abstract class BaseAuthPresenter extends BasePresenter
{

	/** @var array<mixed> */
	public array $alerts = [];

	#[Inject]
	public EntityManagerDecorator $em;

	#[Inject]
	public UserRepository $userRepository;

	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Auth:Sign:in', ['backlink' => $this->storeMyRequest('+14 days')]);
		}
	}

	public function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->appError = false;

		$user = $this->userRepository->get((int) $this->getUser()->getId());
		$user->setLastVisitDate(new DateTime());
		$this->em->flush();

		$this->template->userEntity = $user;

		$this->template->alerts = ArrayHash::from($this->alerts);

		$customRequest = $this->getSession()->getSection('customRequest');

		/** @var ArrayIterator<int, SessionSection> $iterator */
		$iterator = $customRequest->getIterator();

		if ($iterator->count() === 0) {
			$rand = Random::generate(5);
			$customRequest->key = $rand;
		}

		$backlink = $this->storeMyRequest('+14days', $customRequest->key);

		$this->template->backlink = $backlink;

		$this->template->sidebarItems = [];
	}

}

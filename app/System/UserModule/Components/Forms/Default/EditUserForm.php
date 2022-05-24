<?php declare(strict_types = 1);

namespace App\System\UserModule\Components\Forms\Default;

use App\System\Components\Forms\FormFactory;
use App\System\Entities\User;
use App\System\Repositories\UserRepository;
use App\System\Services\Emails\RegistrationNotificationSender;
use App\System\Services\UserService;
use App\Utils\Components\AppComponentInterface;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;

/**
 * @property Template $template
 */
final class EditUserForm extends Control implements AppComponentInterface
{

	public function __construct(
		private FormFactory $formFactory,
		private RegistrationNotificationSender $registrationNotificationSender,
		private UserRepository $userRepository,
		private UserService $userService
	)
	{
	}

	/**
	 * @param array<mixed> $params
	 */
	public function render(array $params = []): void
	{
		$action = $this->getPresenter()->getAction();

		if ($action === 'edit') {
			$user = $this->userRepository->getUserArray((int) $this->getPresenter()->getParameter('id'));

			if ($user === null || count($user) === 0) {
				throw new InvalidArgumentException("Such user doesn't exist!");
			}

			$this['editUserForm']->setDefaults($user);
		}

		$this->template->setFile(__DIR__ . '/editUserForm.latte')
			->render();
	}

	protected function createComponentEditUserForm(): Form
	{
		$action = $this->getPresenter()->getAction();

		$form = $this->formFactory->create();

		$form->addText('username', 'Uživatelské jméno')
			->setRequired();

		$form->addText('email', 'E-mail')
			->addRule(Form::EMAIL, 'Zadaný e-mail má špatný formát')
			->setRequired();

		$roles = [
			'admin' => 'administrátor',
			'user' => 'uživatel',
		];
		$form->addSelect('role', 'Role', $roles)
			->setPrompt('-- Vyberte --')
			->setRequired();

		if ($action === 'edit') {
			$form->addHidden('user_id', $this->getPresenter()->getParameter('id'));
		}

		$form->addSubmit('send', 'OK');

		if ($action === 'edit') {
			$form->onValidate[] = [$this, 'validateEditUserForm'];
		} else {
			$form->onValidate[] = [$this, 'validateAddUserForm'];
		}

		$form->onSuccess[] = [$this, 'editUserFormSucceeded'];

		return $form;
	}

	public function validateEditUserForm(Form $form): void
	{
		$values = $form->getValues();

		if (array_key_exists('user_id', (array) $values)) {
			$user = $this->userRepository->get((int) $values['user_id']);

			if ($user->getEmail() !== $values['email']) {
				$users = $this->userRepository->fetchUsersArray();

				foreach ($users as $item) {
					if (in_array($values['email'], $item, true)) {
						$form->addError('Zadaný email již existuje! Zvojte jiný');
					}
				}
			}

			if ($user->getUsername() !== $values['username']) {
				$users = $this->userRepository->fetchUsersArray();

				foreach ($users as $item) {
					if (in_array($values['username'], $item, true)) {
						$form->addError('Zadané uživatelské jméno již existuje! Zvojte jiné');
					}
				}
			}
		}
	}

	public function validateAddUserForm(Form $form): void
	{
		$values = $form->getValues();

		$user = $this->userRepository->findOneBy(['username' => $values['username']]);

		if ($user instanceof User) {
			$form->addError('Zadané uživatelské jméno již existtuje! Zvojte jiné');
		}

		$user = $this->userRepository->findOneBy(['email' => $values['email']]);

		if ($user instanceof User) {
			$form->addError('Zadaná e-mailová adresa již existtuje! Zvojte jinou');
		}
	}

	public function editUserFormSucceeded(Form $form, ArrayHash $values): void
	{
		if (isset($values->user_id)) {
			$this->userService->updateUserBy(['id' => $values->user_id], $values);
			$this->getPresenter()->flashMessage('Uživatel byl úspěšně upraven', 'success');
			$this->getPresenter()->redirect('Default:');
		} else {
			$values->action = 'add';
			$this->registrationNotificationSender->sendRegistrationNotification($values);
			$user = $this->userService->insertUser($values);

			$this->getPresenter()->redirect('Default:edit#address', ['id' => $user->getId()]);
		}
	}

}

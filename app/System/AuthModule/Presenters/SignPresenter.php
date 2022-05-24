<?php declare(strict_types = 1);

namespace App\System\AuthModule\Presenters;

use App\System\AuthModule\Components\Sign\Forms\ForgottenPasswordForm;
use App\System\AuthModule\Components\Sign\Forms\RecoveryPasswordForm;
use App\System\AuthModule\Components\Sign\Forms\SignInForm;
use App\System\Entities\User;
use App\System\Presenters\BasePresenter;
use App\System\Repositories\UserRepository;
use App\System\Services\Emails\RegistrationNotificationSender;
use App\System\Services\UserService;
use Nette\Application\Attributes\Persistent;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Form;
use Nette\DI\Attributes\Inject;
use Nette\Mail\MailerInterface;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;

final class SignPresenter extends BasePresenter
{

	#[Inject]
	public MailerInterface $mailer;

	#[Inject]
	public SignInForm $signInFactory;

	#[Inject]
	public ForgottenPasswordForm $forgottenPasswordFormFactory;

	#[Inject]
	public RegistrationNotificationSender $registrationNotificationSender;

	#[Inject]
	public RecoveryPasswordForm $recoveryPasswordFormFactory;

	#[Inject]
	public UserRepository $userRepository;

	#[Inject]
	public UserService $userService;

	#[Persistent]
	public string $backlink = '';

	#[Inject]
	public LinkGenerator $linkGenerator;

	public function beforeRender(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->flashMessage('Již jste přihlášen', 'warning');
			$this->redirect(':System:About:');
		}
	}

	protected function createComponentSignInForm(): Form
	{
		$form = $this->signInFactory->create();

		$form->onSuccess[] = function ($form): void {
			$form->getPresenter()->flashMessage('Přihlášení proběhlo úspěšně', 'success');
			$this->restoreRequest($this->backlink);
			$form->getPresenter()->redirect(':System:Default:');
		};

		return $form;
	}

	public function renderRecoveryPassword(?string $username, ?string $token): void
	{
		$sqlUser = $this->userRepository->findOneBy(['username' => $username, 'recoveryToken' => $token]);

		$this->template->username = $username;
		$this->template->token = $token;
		$this->template->sql_user = $sqlUser;

		if (!isset($token) || !isset($sqlUser)) {
			$this->flashMessage('Špatný odkaz na obnovení hesla', 'warning');
			$this->redirect('Sign:in');
		}

		if ($sqlUser->getRecoveryToken() === null) {
			$this->flashMessage('Heslo již bylo obnoveno', 'warning');
			$this->redirect('Sign:in');
		}
	}

	public function createComponentForgottenPasswordForm(): Form
	{
		$form = $this->forgottenPasswordFormFactory->create();

		$form->onSuccess[] = function ($form, $values): void {
			$user = $this->userRepository->findOneBy(['email' => $values->email]);

			if ($user instanceof User) {
				$values->username = $user->getUsername();
				$values->recoveryToken = sha1(Random::generate());
				$values->action = 'recovery';

				$this->registrationNotificationSender->sendRegistrationNotification($values);

				$this->userService->updateUserBy(['email' => $values->email], $values);

				$this->getPresenter()->flashMessage('Instrukce byly odeslány na zadaný e-mail', 'success');
				$this->getPresenter()->redirect('Sign:in');
			} else {
				$form->addError('Zadaná e-mailová adresa neexistuje!');
			}
		};

		return $form;
	}

	public function createComponentRecoveryPasswordForm(): Form
	{
		$form = $this->recoveryPasswordFormFactory->create();

		$userService = $this->userService;

		$form->onSuccess[] = function ($form, $values) use ($userService): void {
			$user = $this->userRepository->findOneBy(
				['username' => $this->getParameter('username'), 'recoveryToken' => $this->getParameter('token')]
			);

			if ($user instanceof User) {
				if ($this->getParameter('token') === $user->getRecoveryToken()
					&& $this->getParameter('username') === $user->getUsername()) {
					$values = ArrayHash::from(['password' => $values->pass, 'recoveryToken' => null]);
					$userService->updateUserBy(['username' => $this->getParameter('username')], $values);
					$this->flashMessage('Heslo bylo úspěšně uloženo', 'success');

					$this->redirect(':Auth:Sign:in');
				} else {
					$form->addError('Jméno nebo token není správný!');
				}
			} else {
				$form->addError('Přístup odepřen');
			}
		};

		return $form;
	}

	public function actionLogout(string $backlink): void
	{
		$this->getUser()->logout();
		$this->restoreRequest($backlink);

		$this->flashMessage('Odhlášení proběhlo úspěšně', 'success');
		$this->redirect('Sign:in');
	}

}

<?php declare(strict_types = 1);

namespace App\System\AuthModule\Components\Sign\Forms;

use App\System\Components\Forms\FormFactory;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

final class SignInForm
{

	public function __construct(
		private FormFactory $formFactory,
		private User $user
	)
	{
	}

	public function create(): Form
	{
		$form = $this->formFactory->create();

		$form->addText('username', 'Uživatelské jméno')
			->setRequired();

		$form->addPassword('password', 'Heslo')
			->setRequired();

		$form->addCheckbox('remember', 'Zapamatovat');

		$form->addSubmit('send', 'OK');

		$form->onSuccess[] = [$this, 'formSucceeded'];

		return $form;
	}

	public function formSucceeded(Form $form, ArrayHash $values): void
	{
		if ($values->remember) {
			$this->user->setExpiration('14 days');
		} else {
			$this->user->setExpiration('20 minutes');
		}

		try {
			$this->user->login($values->username, $values->password);
		} catch (AuthenticationException) {
			$form->addError('Bylo zadáno špatné uživatelské jméno nebo heslo');
		}
	}

}

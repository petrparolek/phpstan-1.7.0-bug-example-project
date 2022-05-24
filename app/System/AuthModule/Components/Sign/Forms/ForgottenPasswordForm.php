<?php declare(strict_types = 1);

namespace App\System\AuthModule\Components\Sign\Forms;

use App\System\Components\Forms\FormFactory;
use Nette\Application\UI\Form;

final class ForgottenPasswordForm
{

	public function __construct(private FormFactory $formFactory)
	{
	}

	public function create(): Form
	{
		$form = $this->formFactory->create();

		$form->addText('email', 'E-mail')
			->setRequired(true)
			->addRule(Form::EMAIL, 'Zadaná e-mailová adresa má špatný tvar!');
		$form->addSubmit('send', 'Odeslat');

		return $form;
	}

}

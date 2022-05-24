<?php declare(strict_types = 1);

namespace App\System\AuthModule\Components\Sign\Forms;

use App\System\Components\Forms\FormFactory;
use Nette\Application\UI;

final class RecoveryPasswordForm
{

	public function __construct(private FormFactory $formFactory)
	{
	}

	public function create(): UI\Form
	{
		$form = $this->formFactory->create();

		$form->addPassword('pass', 'Heslo:')
			->setRequired();
		$form->addPassword('passb', 'Znovu heslo:')
			->setRequired()
			->addRule(UI\Form::EQUAL, 'Hesla nesouhlasí', $form['pass']);

		$form->addSubmit('send', 'OK');

		return $form;
	}

}

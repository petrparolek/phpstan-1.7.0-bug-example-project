<?php declare(strict_types = 1);

namespace App\System\Services\Emails;

use Nette\Application\AbortException;
use Nette\Http\Request;
use Nette\Mail\SendException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use Tracy\ILogger;

final class RegistrationNotificationSender
{

	public function __construct(
		private BaseEmailSender $emailMailer,
		private Request $httpRequest,
		private ILogger $logger
	)
	{
	}

	public function sendRegistrationNotification(ArrayHash $values): void
	{
		$uri = $this->httpRequest->getUrl();

		if ($values->action === 'add') {
			$subject = 'Vytvoření uživatelského účtu na serveru ' . $uri->host;
			$templateFile = __DIR__ . '/templates/addUser.latte';
		} elseif ($values->action === 'recovery') {
			$subject = 'Obnovení hesla na serveru ' . $uri->host;
			$templateFile = __DIR__ . '/templates/forgottenPassword.latte';
		} else {
			throw new AbortException('Action have to be set');
		}

		$token = Random::generate();

		$token = sha1($token);
		$values->recoveryToken = $token;
		$uri = $this->httpRequest->getUrl();

		$params = [
			'recoveryToken' => $values->recoveryToken,
			'url' => $uri->scheme . '://' . $uri->host,
			'username' => $values->username,
		];

		try {
			$this->emailMailer->sendMail(
				to: $values->email,
				subject: $subject,
				templateFile: $templateFile,
				params: $params
			);
		} catch (SendException $e) {
			$this->logger->log(
				'Error while sending email to ' . $values->email . '. Reason: ' . $e->getMessage(),
				ILogger::CRITICAL
			);

			throw new SendException('Unable to send email to ' . $values->email);
		}
	}

}

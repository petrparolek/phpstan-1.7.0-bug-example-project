<?php declare(strict_types = 1);

namespace App\System\Services\Emails;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\TemplateFactoryInterface;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Mail\MailerInterface;
use Nette\Mail\Message;
use Nette\Utils\Validators;

final class BaseEmailSender
{

	public function __construct(
		public string $mailFrom,
		private LinkGenerator $linkGenerator,
		private TemplateFactoryInterface $templateFactory,
		private MailerInterface $mailer
	)
	{
	}

	/**
	 * @param array<mixed> $params
	 * @param array<mixed>|null $attachments
	 */
	public function sendMail(
		string $to,
		string $subject,
		string $templateFile,
		array $params,
		?string $bcc = null,
		?string $mailReplyTo = null,
		?string $mailReplyToSubject = null,
		?array $attachments = null
	): void
	{
		/** @var Template $template */
		$template = $this->createMailTemplate();
		$html = $template->renderToString($templateFile, $params);

		$mail = new Message();

		$mail->setFrom($this->mailFrom)
			->addTo($to);

		if ($mailReplyTo !== null) {
			$mail->addReplyTo($mailReplyTo, $mailReplyToSubject);
		}

		$mail->setSubject($subject)
			->setHtmlBody($html);

		if ($attachments !== null) {
			foreach ($attachments as $attachment) {
				$mail->addAttachment($attachment);
			}
		}

		if (is_string($bcc) && Validators::isEmail($bcc)) {
			$mail->addBcc($bcc);
		}

		$this->mailer->send($mail);
	}

	private function createMailTemplate(): Template
	{
		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);

		return $template;
	}

}

<?php declare(strict_types = 1);

namespace App\System\Presenters;

use Nette;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\LoggerInterface;
use function preg_match;

final class ErrorPresenter implements Nette\Application\IPresenter
{

	use Nette\SmartObject;

	public function __construct(private LoggerInterface $logger)
	{
	}

	public function run(Nette\Application\Request $request): Nette\Application\ResponseInterface
	{
		$e = $request->getParameter('exception');

		if ($e instanceof Nette\Application\BadRequestException) {
			// $this->logger->log("HTTP code {$e->getCode()}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			[$module, , $sep] = Nette\Application\Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';

			return new Responses\ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->log($e, LoggerInterface::EXCEPTION);

		return new Responses\CallbackResponse(
			static function (Http\RequestInterface $httpRequest, Http\IResponse $httpResponse): void {
				if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type')) > 0) {
					require __DIR__ . '/templates/Error/500.phtml';
				}
			}
		);
	}

}

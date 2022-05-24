<?php declare(strict_types = 1);

namespace App\System\Presenters;

use App\Utils\GitParser;
use Nette\DI\Attributes\Inject;
use Nette\DI\Container;

final class AboutPresenter extends BaseAuthPresenter
{

	#[Inject]
	public Container $container;

	public function renderDefault(): void
	{
		$file = $this->container->parameters['appDir'] . '/../VERSION';

		if (file_exists($file)) {
			$version = file_get_contents($file);
		} else {
			$gitParser = new GitParser();

			$version = $gitParser->getCurrentCommitHash() !== ''
				? '#' . $gitParser->getCurrentCommitHash()
					. ' (branch: ' . $gitParser->getCurrentBranchName() . ')'
				: 'N/A';
		}

		$this->template->version = $version;
	}

}

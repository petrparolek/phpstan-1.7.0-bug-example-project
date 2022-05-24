<?php declare(strict_types = 1);

namespace App\Utils;

final class GitParser
{

	private bool $read = false;

	private ?string $dir = null;

	private ?string $branch = null;

	private ?string $commit = null;

	public function getCurrentBranchName(): string
	{
		$this->parseHead();

		if (strlen((string) $this->branch) > 0) {
			return trim((string) $this->branch);
		}

		if (strlen((string) $this->commit) > 0) {
			return 'detached';
		}

		return 'not versioned';
	}

	public function getCurrentCommitHash(): string
	{
		$this->parseHead();

		if (is_string($this->commit)) {
			return substr($this->commit, 0, 8);
		}

		return '';
	}

	private function parseHead(): void
	{
		if (!$this->read) {
			$dir = $this->findGitDir();

			$head = $dir . '/HEAD';

			if (is_string($dir) && is_readable($head)) {
				$branch = (string) file_get_contents($head);

				if (str_starts_with($branch, 'ref:')) {
					$parts = explode('/', $branch, 3);
					$this->branch = $parts[2];

					$commitFile = $dir . '/' . trim(substr($branch, 5, strlen($branch)));

					if (is_readable($commitFile)) {
						$this->commit = (string) file_get_contents($commitFile);
					}
				} else {
					$this->commit = $branch;
				}
			}

			$this->read = true;
		}
	}

	private function findGitDir(): ?string
	{
		if (is_string($this->dir)) {
			return $this->dir;
		}

		$scriptPath = $_SERVER['SCRIPT_FILENAME'];
		$dir = realpath(dirname($scriptPath));

		while ($dir !== false) {
			flush();
			$currentDir = $dir;
			$dir .= '/..';
			$dir = realpath($dir);
			$gitDir = $dir . '/.git';

			if (is_dir($gitDir)) {
				$this->dir = $gitDir;

				return $gitDir;
			}

			// Stop recursion to parent on root directory
			if ($dir === $currentDir) {
				break;
			}
		}

		return null;
	}

}

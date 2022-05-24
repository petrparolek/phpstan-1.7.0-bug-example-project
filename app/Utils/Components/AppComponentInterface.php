<?php declare(strict_types = 1);

namespace App\Utils\Components;

interface AppComponentInterface
{

	/** @param array<mixed> $param */
	public function render(array $param = []): void;

}

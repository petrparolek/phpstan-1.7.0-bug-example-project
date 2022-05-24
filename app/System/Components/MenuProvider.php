<?php declare(strict_types = 1);

namespace App\System\Components;

use App\Utils\Arrays;

final class MenuProvider
{

	/** @param array<mixed> $menuItems */
	public function __construct(public array $menuItems = [])
	{
	}

	/** @return array<mixed> */
	public function getMenuItems(): array
	{
		Arrays::sortRecurse($this->menuItems);

		return $this->menuItems;
	}

}

<?php declare(strict_types = 1);

namespace App\Utils;

class Arrays
{

	/**
	 * @param array<mixed> $array
	 */
	public static function sortRecurse(array &$array): void
	{
		usort(
			$array,
			fn ($a, $b) => $a['order'] - $b['order']
		);

		foreach ($array as &$subarray) {
			if (isset($subarray['subitems'])) {
				self::sortRecurse($subarray['subitems']);
			}
		}
	}

}

<?php declare(strict_types = 1);

namespace App\Utils;

final class Numbers
{

	public static function formatAmount(float|int $amount): string
	{
		return floor($amount) === $amount
			? number_format($amount, 0, ',', '&nbsp;')
			: number_format($amount, 2, ',', '&nbsp;');
	}

}

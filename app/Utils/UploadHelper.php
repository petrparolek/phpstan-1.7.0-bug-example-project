<?php declare(strict_types = 1);

namespace App\Utils;

final class UploadHelper
{

	public const ABSOLUTE_UPLOADED_DIR = 'absoluteUploadedDir';

	public const UPLOADED_DIR = 'uploadedDir';

	public function __construct(public string $absuluteUploadedDir)
	{
	}

	/**
	 * @return string[]
	 */
	public function getInvoiceFilePaths(int $userId, int $year, string $type): array
	{
		$uploadedDir = (string) $year . DIRECTORY_SEPARATOR . 'invoices' . DIRECTORY_SEPARATOR . $type;

		return [
			'absoluteUploadedDir' => $this->absuluteUploadedDir . DIRECTORY_SEPARATOR . (string) $userId
				. DIRECTORY_SEPARATOR . $uploadedDir,
			'uploadedDir' => $uploadedDir,
		];
	}

}

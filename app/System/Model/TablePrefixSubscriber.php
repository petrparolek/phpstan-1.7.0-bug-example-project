<?php declare(strict_types = 1);

namespace App\System\Model;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

final class TablePrefixSubscriber implements EventSubscriber
{

	public function __construct(public string $prefix)
	{
	}

	/**
	 * @return array<string>
	 */
	public function getSubscribedEvents(): array
	{
		return ['loadClassMetadata'];
	}

	public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
	{
		$classMetadata = $args->getClassMetadata();

		if (strlen($this->prefix) > 0) {
			// Only add the prefixes to our own entities.
			//if (FALSE !== strpos($classMetadata->namespace, 'Some\Namespace\Part')) {
			// Do not re-apply the prefix when the table is already prefixed
			if (!str_contains($classMetadata->getTableName(), $this->prefix)) {
				$tableName = $this->prefix . $classMetadata->getTableName();
				$classMetadata->setPrimaryTable(['name' => $tableName]);
			}

			foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
				if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide'] === true) {
					$mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];

					// Do not re-apply the prefix when the association is already prefixed
					if (str_contains($mappedTableName, $this->prefix)) {
						continue;
					}

					$classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
				}
			}
		}
	}

}

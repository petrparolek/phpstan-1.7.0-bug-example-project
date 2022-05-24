<?php declare(strict_types = 1);

namespace App\System\Repositories;

use App\System\Entities\User;
use App\System\Model\EntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;

/**
 * @extends EntityRepository<User>
 */
final class UserRepository extends EntityRepository
{

	/**
	 * @return ?array<string>
	 * @throws NonUniqueResultException
	 */
	public function getUserArray(int $id): ?array
	{
		$query = $this->createQueryBuilder('u')
			->where('u.id = :id')
			->setParameter('id', $id)
			->getQuery();

		return $query->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
	}

	/**
	 * @return array<array<string>>
	 */
	public function fetchUsersArray(): array
	{
		return $this->createQueryBuilder('u')
			->select('u')
			->getQuery()
			->getResult(Query::HYDRATE_ARRAY);
	}

}

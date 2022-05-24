<?php declare(strict_types = 1);

namespace App\System\Services;

use App\System\Entities\User;
use App\System\Model\EntityManagerDecorator;
use App\System\Repositories\UserRepository;
use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;

final class UserService
{

	public function __construct(
		private EntityManagerDecorator $em,
		private UserRepository $userRepository
	)
	{
	}

	public function insertUser(ArrayHash $values): User
	{
		$user = new User();

		$user->setUsername($values->username);
		$user->setEmail($values->email);
		$user->setRole($values->role);
		$user->setRecoveryToken($values->recoveryToken);

		$this->em->persist($user);

		$this->em->flush();

		return $user;
	}

	/**
	 * @param array<mixed> $criteria
	 * @throws BadRequestException
	 */
	public function updateUserBy(array $criteria, ArrayHash $values): User
	{
		$user = $this->userRepository->findOneBy($criteria);

		if ($user instanceof User) {
			$user->setUsername($values->username ?? $user->getUsername());
			$user->setEmail($values->email ?? $user->getEmail());
			$user->setRole($values->role ?? $user->getRole());

			if (isset($values->password)) {
				$user->setPassword($values->password);
			}

			$user->setRecoveryToken($values->recoveryToken ?? null);

			$this->em->flush();

			return $user;
		}

		throw new BadRequestException("Such user doesn't exist!");
	}

	public function deleteUser(int $id): void
	{
		$user = $this->userRepository->find($id);

		if ($user instanceof User) {
			$this->em->remove($user);

			$this->em->flush();
		} else {
			throw new BadRequestException("Such user doesn't exist!");
		}
	}

}

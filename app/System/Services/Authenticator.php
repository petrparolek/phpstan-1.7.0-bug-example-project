<?php declare(strict_types = 1);

namespace App\System\Services;

use App\System\Entities\User;
use App\System\Repositories\UserRepository;
use Nette\Application\BadRequestException;
use Nette\Security as NS;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

final class Authenticator implements NS\AuthenticatorInterface
{

	public function __construct(
		private Passwords $passwords,
		private UserRepository $userRepository,
		private UserService $userService
	)
	{
	}

	/**
	 * @throws BadRequestException
	 * @throws NS\AuthenticationException
	 */
	public function authenticate(string $username, string $password): NS\IdentityInterface
	{
		$user = $this->userRepository->findOneBy(['username' => $username]);

		if (!$user instanceof User) {
			throw new NS\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if (!$this->passwords->verify($password, (string) $user->getPassword())) {
			throw new NS\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		if ($this->passwords->needsRehash((string) $user->getPassword())) {
			$data = ArrayHash::from(['password' => $password, 'recoveryToken' => null]);
			$this->userService->updateUserBy(['id' => $user->getId()], $data);
		}

		return new NS\SimpleIdentity(
			$user->getId(),
			[
				$user->getRole(),
			],
			[
				'username' => $user->getUsername(),
			]
		);
	}

}

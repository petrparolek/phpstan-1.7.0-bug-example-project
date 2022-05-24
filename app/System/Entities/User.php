<?php declare(strict_types = 1);

namespace App\System\Entities;

use App\System\Repositories\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{

	#[ORM\Id]
	#[ORM\Column(type: 'integer')]
	#[ORM\GeneratedValue]
	private int $id;

	#[ORM\Column(type:'string', nullable: false)]
	private string $username;

	#[ORM\Column(type:'string', nullable: true)]
	private ?string $password = null;

	#[ORM\Column(type:'string', nullable: false)]
	private string $email;

	#[ORM\Column(type:'string', nullable: false)]
	private string $role;

	#[ORM\Column(type:'datetime', nullable: true)]
	private ?DateTime $registerDate = null;

	#[ORM\Column(type:'datetime', nullable: true)]
	private ?DateTime $lastVisitDate = null;

	#[ORM\Column(type:'string', nullable: true)]
	private ?string $recoveryToken = null;

	public function __construct()
	{
		$this->registerDate = new DateTime();
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getRole(): string
	{
		return $this->role;
	}

	public function getRegisterDate(): ?DateTime
	{
		return $this->registerDate;
	}

	public function getLastVisitDate(): ?DateTime
	{
		return $this->lastVisitDate;
	}

	public function getRecoveryToken(): ?string
	{
		return $this->recoveryToken;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function setPassword(string $password): void
	{
		$passwords = new Passwords();
		$this->password = $passwords->hash($password);
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function setRole(string $role): void
	{
		$this->role = $role;
	}

	public function setRegisterDate(DateTime $registerDate): void
	{
		$this->registerDate = $registerDate;
	}

	public function setLastVisitDate(DateTime $lastVisitDate): void
	{
		$this->lastVisitDate = $lastVisitDate;
	}

	public function setRecoveryToken(?string $recoveryToken): void
	{
		$this->recoveryToken = $recoveryToken;
	}

}

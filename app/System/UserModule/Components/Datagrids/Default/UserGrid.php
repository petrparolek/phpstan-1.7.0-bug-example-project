<?php declare(strict_types = 1);

namespace App\System\UserModule\Components\Datagrids\Default;

use App\System\Components\Datagrids\BaseDataGrid;
use App\System\Model\EntityManagerDecorator;
use App\System\Repositories\UserRepository;
use App\System\Services\UserService;
use Nette\Localization\TranslatorInterface;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

final class UserGrid extends BaseDataGrid
{

	public EntityManagerDecorator $em;

	public function __construct(
		private TranslatorInterface $translator,
		private UserRepository $userRepository,
		private UserService $userService
	)
	{
	}

	public function createComponentDataGrid(string $name): void
	{
		$grid = new DataGrid($this, $name);

		$grid->setDataSource($this->userRepository->createQueryBuilder('er'));

		$grid->addColumnNumber('id', 'user_datagrid.id');
		$grid->addColumnText('username', 'user_datagrid.username');
		$grid->addColumnText('email', 'user_datagrid.email');
		$grid->addColumnText('role', 'user_datagrid.role')
			->setReplacement(
				[
					'admin' => 'administrátor',
					'driver' => 'řidič',
					'user' => 'uživatel',
				]
			);

		$grid->addAction('Default:edit', 'ublaboo_datagrid.edit')
			->setIcon('pencil');

		$grid->addAction('delete', '', 'delete!')
			->setIcon('trash')
			->setTitle('ublaboo_datagrid.delete')
			->setClass('btn btn-xs btn-danger ajax')
			->setConfirmation(new StringConfirmation('user_datagrid.confirm_delete', 'username'));

		$grid->addToolbarButton('Default:add', 'ublaboo_datagrid.add');

		$grid->setTranslator($this->translator);
	}

	public function handleDelete(int $id): void
	{
		$this->getPresenter()->flashMessage('Uživatel byl úspěšně odstraněn', 'error');

		$this->userService->deleteUser($id);

		if ($this->getPresenter()->isAjax()) {
			$this->getPresenter()->redrawControl('flashes');
			/** @var DataGrid $userGrid */
			$userGrid = $this['dataGrid'];
			$userGrid->reload();
		} else {
			$this->getPresenter()->redirect('this');
		}
	}

}

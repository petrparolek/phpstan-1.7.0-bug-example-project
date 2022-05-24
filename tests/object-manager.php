<?php declare(strict_types = 1);

return App\Bootstrap::boot()
	->createContainer()
	->getByType(App\System\Model\EntityManagerDecorator::class);

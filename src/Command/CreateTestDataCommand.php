<?php

declare(strict_types=1);

namespace App\Command;

use App\DataFixtures\RoomFixture;
use App\DataFixtures\UserFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTestDataCommand extends Command
{
    protected static $defaultName = 'app:create-test-data';

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loader = new Loader();
        $loader->addFixture(new UserFixture());
        $loader->addFixture(new RoomFixture());

        $purger   = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);

        $executor->execute($loader->getFixtures(), true);

        $output->writeln('Created successfully!');

        return Command::SUCCESS;
    }
}

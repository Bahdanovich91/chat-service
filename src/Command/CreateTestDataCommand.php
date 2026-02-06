<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Room;
use App\Entity\User;
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
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setName("User $i");
            $this->entityManager->persist($user);
        }

        $roomNames = ['test1', 'test2', 'test3', 'test4', 'test5'];
        foreach ($roomNames as $name) {
            $room = new Room();
            $room->setName($name);
            $this->entityManager->persist($room);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}

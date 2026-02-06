<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class RoomService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoomRepository         $roomRepository
    ) {}

    public function getRoom(int $roomId): ?Room
    {
        return $this->roomRepository->find($roomId);
    }

    public function getAllRooms(): array
    {
        $rooms = $this->roomRepository->findAll();

        return array_map(function (Room $room) {
            return [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'messageCount' => $room->getMessages()->count()
            ];
        }, $rooms);
    }

    public function createRoom(string $name): Room
    {
        $room = new Room();
        $room->setName($name);

        $this->entityManager->persist($room);
        $this->entityManager->flush();

        return $room;
    }
}

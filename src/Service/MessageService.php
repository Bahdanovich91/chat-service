<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Exceptions\RoomNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class MessageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository      $messageRepository,
        private RoomRepository         $roomRepository,
        private UserRepository         $userRepository,
    ) {}

    public function save(int $roomId, int $userId, string $content): Message
    {
        $room = $this->roomRepository->find($roomId);
        if (!$room) {
            throw new RoomNotFoundException($roomId);
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        $message = new Message();
        $message->setContent($content);
        $message->setSender($user);
        $message->setRoom($room);
        $message->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function getRecentMessages(int $roomId, ?int $minutes = 15): array
    {
        $dateLimit = new \DateTimeImmutable("-$minutes minutes");
        $messages = $this->messageRepository->findByRoomAndDate($roomId, $dateLimit);

        return array_map(function (Message $message) {
            return [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'senderId' => $message->getSender()->getId(),
                'senderName' => $message->getSender()->getName(),
                'roomId' => $message->getRoom()->getId(),
                'createdAt' => $message->getCreatedAt()->format('c')
            ];
        }, $messages);
    }
}

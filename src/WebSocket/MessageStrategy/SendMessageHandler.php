<?php

declare(strict_types=1);

namespace App\WebSocket\MessageStrategy;

use App\Enum\ActionType;
use App\Exceptions\RoomNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\MessageService;
use App\Service\RoomService;
use App\WebSocket\ChatServerHandler;
use App\WebSocket\Dto\SendMessageDto;
use Ratchet\ConnectionInterface;

readonly class SendMessageHandler implements WebSocketStrategyInterface
{
    public function __construct(
        private MessageService $messageService,
        private RoomService    $roomService,
        private UserRepository $userRepository
    ) {
    }

    public function isApplicable(string $type): bool
    {
        return ActionType::SendMessage->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        try {
            $dto = SendMessageDto::fromArray($data);

            $room = $this->roomService->getRoom($dto->roomId);
            if (!$room) {
                throw new RoomNotFoundException($dto->roomId);
            }

            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                throw new UserNotFoundException($dto->userId);
            }

            $message = $this->messageService->save(
                $dto->roomId,
                $dto->userId,
                $dto->message
            );

            $server->broadcast($dto->roomId, [
                'type'       => ActionType::SendMessage->value,
                'roomId'     => $dto->roomId,
                'messageId'  => $message->getId(),
                'content'    => $dto->message,
                'senderId'   => $dto->userId,
                'senderName' => $user->getName(),
                'timestamp'  => $message->getCreatedAt()->format('c')
            ]);
        } catch (\Exception $e) {
            $conn->send(json_encode([
                'type'    => ActionType::Error->value,
                'message' => $e->getMessage()
            ]));
        }
    }
}

<?php

declare(strict_types=1);

namespace App\WebSocket\Strategy;

use App\Enum\ActionType;
use App\WebSocket\ChatServerHandler;
use App\WebSocket\Dto\RoomActionDto;
use Ratchet\ConnectionInterface;
use App\Service\MessageService;
use App\Service\RoomService;

readonly class JoinRoomHandler implements WebSocketStrategyInterface
{
    public function __construct(
        private MessageService $messageService,
        private RoomService    $roomService
    ) {
    }

    public function isApplicable(string $type): bool
    {
        return ActionType::JoinRoom->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        try {
            $dto = RoomActionDto::fromArray($data);

            $room = $this->roomService->getRoom($dto->roomId);
            if (!$room) {
                return;
            }

            $history = $this->messageService->getRecentMessages($dto->roomId);

            $conn->send(json_encode([
                'type' => 'room_history',
                'roomId' => $dto->roomId,
                'roomName' => $room->getName(),
                'messages' => $history
            ]));

            $server->joinRoom($dto->roomId, $conn, $dto->userId);
            $server->broadcast($dto->roomId, [
                'type' => 'user_joined',
                'roomId' => $dto->roomId,
                'userId' => $dto->userId,
                'timestamp' => date('c')
            ]);
        } catch (\Exception $e) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => $e->getMessage()
            ]));
        }
    }
}

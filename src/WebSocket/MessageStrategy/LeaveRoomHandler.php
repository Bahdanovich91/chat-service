<?php

declare(strict_types=1);

namespace App\WebSocket\MessageStrategy;

use App\Enum\ActionType;
use App\Exceptions\RoomNotFoundException;
use App\Service\RoomService;
use App\WebSocket\ChatServerHandler;
use App\WebSocket\Dto\RoomActionDto;
use Ratchet\ConnectionInterface;

readonly class LeaveRoomHandler implements WebSocketStrategyInterface
{
    public function __construct(
        private RoomService $roomService
    ) {
    }

    public function isApplicable(string $type): bool
    {
        return ActionType::LeaveRoom->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        try {
            $dto = RoomActionDto::fromArray($data);

            $room = $this->roomService->getRoom($dto->roomId);
            if (!$room) {
                throw new RoomNotFoundException($dto->roomId);
            }

            $server->leaveRoom($dto->roomId, $conn);
            $server->broadcast($dto->roomId, [
                'type'      => 'user_left',
                'roomId'    => $dto->roomId,
                'userId'    => $dto->userId,
                'timestamp' => date('c')
            ]);
        } catch (\Exception $e) {
            $conn->send(json_encode([
                'type'    => 'error',
                'message' => $e->getMessage()
            ]));
        }
    }
}

<?php

declare(strict_types=1);

namespace App\WebSocket\Strategy;

use App\Enum\ActionType;
use App\WebSocket\ChatServerHandler;
use App\WebSocket\Dto\RoomActionDto;
use Ratchet\ConnectionInterface;
use App\Service\RoomService;

class LeaveRoomHandler implements WebSocketStrategyInterface
{
    public function isApplicable(string $type): bool
    {
        return ActionType::LeaveRoom->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        $dto = RoomActionDto::fromArray($data);
//        if (!$roomId) {
//            return;
//        }

        $server->leaveRoom($dto->roomId, $conn);
        $server->broadcast($dto->roomId, [
            'type' => 'user_left',
            'roomId' => $dto->roomId,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\WebSocket\Strategy;

use App\Enum\ActionType;
use App\WebSocket\ChatServerHandler;
use Ratchet\ConnectionInterface;
use App\Service\RoomService;

class ListRoomsHandler implements WebSocketStrategyInterface
{
    public function isApplicable(string $type): bool
    {
        return ActionType::ListRooms->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        $conn->send(json_encode([
            'type' => 'rooms',
            'rooms' => array_keys($server->getRooms()),
        ]));
    }
}

<?php

declare(strict_types=1);

namespace App\WebSocket\MessageStrategy;

use App\Enum\ActionType;
use App\WebSocket\ChatServerHandler;
use Ratchet\ConnectionInterface;
use App\Service\RoomService;

readonly class ListRoomsHandler implements WebSocketStrategyInterface
{
    public function __construct(
        private RoomService $roomService
    ) {
    }

    public function isApplicable(string $type): bool
    {
        return ActionType::ListRooms->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        $rooms = $this->roomService->getAllRooms();

        $conn->send(json_encode([
            'type' => 'rooms_list',
            'rooms' => $rooms,
            'timestamp' => date('c')
        ]));
    }
}

<?php

declare(strict_types=1);

namespace App\WebSocket\Strategy;

use App\Enum\ActionType;
use App\WebSocket\ChatServerHandler;
use App\WebSocket\Dto\SendMessageDto;
use Ratchet\ConnectionInterface;
use App\Service\MessageService;

class SendMessageHandler implements WebSocketStrategyInterface
{
    public function isApplicable(string $type): bool
    {
        return ActionType::SendMessage->value === $type;
    }

    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void
    {
        $dto = SendMessageDto::fromArray($data);
//        if (!$roomId || !$message) {
//            return;
//        }

        $server->broadcast($dto->roomId, [
            'type' => 'new_message',
            'roomId' => $dto->roomId,
            'message' => $dto->message,
        ]);
    }
}

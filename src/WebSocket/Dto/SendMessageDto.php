<?php

declare(strict_types=1);

namespace App\WebSocket\Dto;

class SendMessageDto implements WebSocketDtoInterface
{
    public function __construct(
        public int $roomId,
        public int $userId,
        public string $message
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['roomId']) || !isset($data['userId']) || !isset($data['message'])) {
            throw new \InvalidArgumentException('Missing required fields');
        }

        return new self(
            (int) $data['roomId'],
            (int) $data['userId'],
            $data['message']
        );
    }
}

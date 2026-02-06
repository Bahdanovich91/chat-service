<?php

declare(strict_types=1);

namespace App\WebSocket\Dto;

class SendMessageDto implements WebSocketDtoInterface
{
    public function __construct(
        public int $roomId,
        public string $message
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['roomId'], $data['message']);
    }
}
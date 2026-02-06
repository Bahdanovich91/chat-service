<?php

declare(strict_types=1);

namespace App\WebSocket\Dto;

class RoomActionDto implements WebSocketDtoInterface
{
    public function __construct(
        public int $roomId
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['roomId']);
    }
}
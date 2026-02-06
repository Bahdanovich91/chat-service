<?php

declare(strict_types=1);

namespace App\WebSocket\Dto;

class RoomActionDto implements WebSocketDtoInterface
{
    public function __construct(
        public int $roomId,
        public int $userId
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['roomId']) || !isset($data['userId'])) {
            throw new \Exception('');
        }

        return new self(
            (int) $data['roomId'],
            (int) $data['userId']
        );
    }
}

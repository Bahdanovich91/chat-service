<?php

declare(strict_types=1);

namespace App\WebSocket\Dto;

interface WebSocketDtoInterface
{
    public static function fromArray(array $data): self;
}

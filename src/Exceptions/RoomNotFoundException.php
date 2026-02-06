<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class RoomNotFoundException extends Exception
{
    public function __construct(int $roomId, string $message = 'Room not found', int $code = 404)
    {
        parent::__construct("$message (ID: $roomId)", $code);
    }
}

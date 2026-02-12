<?php

declare(strict_types=1);

namespace App\Enum;

enum ActionType: string
{
    case JoinRoom    = 'join_room';
    case LeaveRoom   = 'leave_room';
    case SendMessage = 'send_message';
    case ListRooms   = 'list_rooms';
    case Error       = 'error';
}

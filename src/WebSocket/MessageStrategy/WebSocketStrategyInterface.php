<?php

declare(strict_types=1);

namespace App\WebSocket\MessageStrategy;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\WebSocket\ChatServerHandler;
use Ratchet\ConnectionInterface;

#[AutoconfigureTag('app.handlers')]
interface WebSocketStrategyInterface
{
    public function isApplicable(string $type): bool;
    public function handle(ConnectionInterface $conn, array $data, ChatServerHandler $server): void;
}

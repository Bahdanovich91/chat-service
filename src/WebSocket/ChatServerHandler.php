<?php

declare(strict_types=1);

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use App\WebSocket\Strategy\WebSocketStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ChatServerHandler implements MessageComponentInterface
{
    private SplObjectStorage $clients;
    private iterable $handlers;
    private array $rooms = [];
    private array $messageHistory = [];
    private array $roomNames = [];

    public function __construct(
        #[TaggedIterator('app.handlers')]
        iterable $handlers
    ) {
        $this->handlers = $handlers;
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn, ['rooms' => []]);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);

        foreach ($this->handlers as $handler) {
            if ($handler->isApplicable($data['type'])) {
                $handler->handle($from, $data, $this);
                return;
            }
        }

        $from->send(json_encode(['type' => 'error', 'message' => 'No handler found']));
    }

    public function onClose(ConnectionInterface $conn): void
    {
        foreach ($this->rooms as $room) {
            $room->detach($conn);
        }

        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Throwable $e): void
    {
        $conn->send(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        $conn->close();
    }

    public function joinRoom(int $roomId, ConnectionInterface $conn): void
    {
        $this->rooms[$roomId] ??= new SplObjectStorage();
        $this->rooms[$roomId]->attach($conn);
        $meta = $this->clients[$conn];
        $meta['rooms'][] = $roomId;
        $this->clients[$conn] = $meta;
    }

    public function leaveRoom(int $roomId, ConnectionInterface $conn): void
    {
        $this->rooms[$roomId]?->detach($conn);
        $meta = $this->clients[$conn];
        $meta['rooms'] = array_filter($meta['rooms'], fn($r) => $r !== $roomId);
        $this->clients[$conn] = $meta;
    }

    public function broadcast(int $roomId, array $payload): void
    {
        if (!isset($this->rooms[$roomId])) {
            return;
        }

        foreach ($this->rooms[$roomId] as $client) {
            $client->send(json_encode($payload));
        }
    }

    public function getRooms(): array
    {
        return $this->rooms;
    }
}

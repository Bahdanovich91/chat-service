<?php

declare(strict_types=1);

namespace App\WebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ChatServerHandler implements MessageComponentInterface
{
    private SplObjectStorage $clients;

    private iterable $handlers;

    private array $rooms = [];

    public function __construct(
        #[TaggedIterator('app.handlers')]
        iterable $handlers
    ) {
        $this->handlers = $handlers;
        $this->clients  = new SplObjectStorage();
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

        $from->send(json_encode(['type' => 'error', 'message' => 'Handler not found']));
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $meta   = $this->clients[$conn];
        $userId = $meta['userId'];

        foreach ($meta['rooms'] as $roomId) {
            $this->rooms[$roomId]?->detach($conn);

            if (isset($this->rooms[$roomId])) {
                $this->broadcast($roomId, [
                    'type'   => 'user_left',
                    'roomId' => $roomId,
                    'userId' => $userId,
                ]);
            }
        }

        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Throwable $e): void
    {
        $conn->send(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        $conn->close();
    }

    public function joinRoom(int $roomId, ConnectionInterface $conn, int $userId): void
    {
        $this->rooms[$roomId] ??= new SplObjectStorage();
        $this->rooms[$roomId]->attach($conn);

        $meta                 = $this->clients[$conn];
        $meta['userId']       = $userId;
        $meta['rooms'][]      = $roomId;
        $this->clients[$conn] = $meta;
    }

    public function leaveRoom(int $roomId, ConnectionInterface $conn): void
    {
        $this->rooms[$roomId]?->detach($conn);

        $meta                 = $this->clients[$conn];
        $meta['rooms']        = array_filter($meta['rooms'], fn($r) => $r !== $roomId);
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
}

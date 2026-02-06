<?php

declare(strict_types=1);

namespace App\Command;

use App\WebSocket\ChatServerHandler;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SecureServer;
use React\Socket\SocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class RunChatServerCommand extends Command
{
    protected static $defaultName = 'app:chat-server';

    public function __construct(
        #[TaggedIterator('app.handlers')]
        private iterable $handlers,
        private readonly string $certPath,
        private readonly string $keyPath,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loop = Loop::get();
        $server = new ChatServerHandler($this->handlers);
        $socket = new SocketServer('0.0.0.0:8443', [], $loop);

        $secureSocket = new SecureServer($socket, $loop, [
            'local_cert' => $this->certPath,
            'local_pk' => $this->keyPath,
            'allow_self_signed' => true,
            'verify_peer' => false,
        ]);

        new IoServer(
            new HttpServer(
                new WsServer($server)
            ),
            $secureSocket,
            $loop
        );

        $output->writeln('wss://localhost:8443');
        $loop->run();

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\WebSocket\ChatServerHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
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
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $server = new ChatServerHandler($this->handlers);

        $ratchet = IoServer::factory(
            new HttpServer(
                new WsServer($server)
            ),
            8080
        );

        $output->writeln('port 8080');
        $ratchet->run();

        return Command::SUCCESS;
    }
}

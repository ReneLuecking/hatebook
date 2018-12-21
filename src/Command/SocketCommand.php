<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use App\Sockets\Chat;

class SocketCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SocketCommand constructor.
     * @param null $name
     * @param EntityManagerInterface $em
     */
    public function __construct($name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setName('sockets:start-chat')
            ->setHelp('Starts the chat socket')
            ->setDescription('Starts the chat socket');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            array(
                'Chat socket',
                '===========',
                'Starting chat, open your browser.',
            )
        );

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat($this->em)
                )
            ),
            8080
        );

        $server->run();
    }
}

<?php

namespace App\Sockets;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\RequestInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Entity\User;
use Ratchet\WebSocket\WsConnection;

class Chat implements MessageComponentInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     * Chat constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->clients = new \SplObjectStorage();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        /** @var WsConnection $conn */
        /** @var RequestInterface $request */
        $request = $conn->httpRequest;
        parse_str($request->getUri()->getQuery(), $query);

        /** @var User $user */
        $user = $this->em->find(User::class, $query[ 'id' ]);

        $conn->user = $user;

        $this->clients->attach($conn);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        error_log("An websocket error has occurred: {$e->getMessage()}");
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg);

        switch ($msg->action) {
            case "get":
                $this->getMessages($from, $msg);
                break;
            case "send":
                $this->sendMessage($from, $msg);
                break;
        }
    }

    private function getMessages($from, $msg)
    {

    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     */
    private function sendMessage(ConnectionInterface $from, $msg)
    {
        /** @var User $to */
        $to = $this->em->find(User::class, $msg->to);

        $message = new \App\Entity\Chat();
        $message->setSender($from->user)
            ->setRecipient($to)
            ->setText($msg->text);

        $this->em->persist($message);
        $this->em->flush();

        $return = new \stdClass();
        $return->action = 'sent';
        $return->id = $message->getId();
        $return->text = $message->getText();
        $return->datetime = $message->getDatetime();
        $return->to = $to->getId();

        $from->send(json_encode($return));

        if ($toConn = $this->findConnectionByUser($to)) {
            $return->action = 'new';
            $return->from = $from->user->getId();
            unset($return->to);

            $toConn->send(json_encode($return));
        }
    }

    /**
     * @param User $user
     * @return null|WsConnection
     */
    private function findConnectionByUser(User $user)
    {
        $this->clients->rewind();

        for ($i = 0; $i < $this->clients->count(); $i++) {
            /** @var WsConnection $conn */
            $conn = $this->clients->current();

            if ($conn->user === $user) {
                return $conn;
            }

            $this->clients->next();
        }

        $this->clients->rewind();

        return null;
    }
}

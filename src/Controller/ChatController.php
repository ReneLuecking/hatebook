<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ChatController
 * @package App\Controller
 */
class ChatController extends Controller
{
    /**
     * @Route("/chat/getmessages", name="get_messages")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetMessages(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $chatRepo = $this->getDoctrine()->getRepository(Chat::class);

        $senderId = (int)$request->request->get('senderId');
        $recipientId = (int)$request->request->get('recipientId');
        $offset = (int)$request->request->get('offset', 0);

        $sender = $em->find(User::class, $senderId);
        $recipient = $em->find(User::class, $recipientId);

        $messages = $chatRepo->findMessagesByUsers($sender, $recipient, $offset);

        $messagesJson = json_encode($messages);

        return new JsonResponse($messagesJson);
    }
}

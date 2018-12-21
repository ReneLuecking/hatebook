<?php

namespace App\Controller;

use App\Entity\Enemy;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnemyController extends Controller
{
    /**
     * @Route("/enemy/add", name="add_enemy")
     * @param Request $request
     * @return Response
     */
    public function ajaxAddEnemy(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        $initiatorId = (int)$request->get('initiatorId');
        $recipientId = (int)$request->get('recipientId');

        /** @var User $initiator */
        $initiator = $userRepo->find($initiatorId);

        /** @var User $recipient */
        $recipient = $userRepo->find($recipientId);

        $enemy = new Enemy();
        $enemy->setInitiator($initiator)
            ->setRecipient($recipient)
            ->setIsAccepted(false);

        $em->persist($enemy);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/enemy/accept", name="accept_enemy")
     * @param Request $request
     * @return Response
     */
    public function ajaxAcceptEnemy(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $enemyRepo = $this->getDoctrine()->getRepository(Enemy::class);

        $initiatorId = (int)$request->get('initiatorId');
        $recipientId = (int)$request->get('recipientId');

        /** @var User $initiator */
        $initiator = $userRepo->find($initiatorId);

        /** @var User $recipient */
        $recipient = $userRepo->find($recipientId);

        $enemy = $enemyRepo->findOneBy(
            array(
                'initiator' => $initiator,
                'recipient' => $recipient,
            )
        );

        $enemy->setIsAccepted(true);

        $em->persist($enemy);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/enemy/remove", name="remove_enemy")
     * @param Request $request
     * @return Response
     */
    public function ajaxRemoveEnemy(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $enemyRepo = $this->getDoctrine()->getRepository(Enemy::class);

        $initiatorId = (int)$request->get('initiatorId');
        $recipientId = (int)$request->get('recipientId');

        /** @var User $initiator */
        $initiator = $userRepo->find($initiatorId);

        /** @var User $recipient */
        $recipient = $userRepo->find($recipientId);

        $enemy_1 = $enemyRepo->findOneBy(
            array(
                'initiator' => $initiator,
                'recipient' => $recipient,
            )
        );


        $enemy_2 = $enemyRepo->findOneBy(
            array(
                'initiator' => $recipient,
                'recipient' => $initiator,
            )
        );

        if ($enemy_1) {
            $em->remove($enemy_1);
            $em->flush();
        } else {
            if ($enemy_2) {
                $em->remove($enemy_2);
                $em->flush();
            }
        }

        return new Response();
    }
}

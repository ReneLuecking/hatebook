<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Entity\Enemy;
use App\Repository\EnemyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add(
                'firstName',
                TextType::class,
                array(
                    'label' => 'Vorname',
                )
            )
            ->add(
                'lastName',
                TextType::class,
                array(
                    'label' => 'Nachname',
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'label' => 'E-Mail',
                )
            )
            ->add(
                'birthday',
                DateType::class,
                array(
                    'label' => 'Geburtstag',
                    'widget' => 'single_text',
                )
            )
            ->add(
                'pictureUpload',
                FileType::class,
                array(
                    'label' => 'Profilbild',
                )
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'first_options' => array(
                        'label' => 'Passwort',
                    ),
                    'second_options' => array(
                        'label' => 'Passwort wiederholen',
                    ),
                )
            )
            ->add(
                'register',
                SubmitType::class,
                array(
                    'label' => 'Registrieren',
                )
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEmail(strtolower($user->getEmail()));

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $file = reset($_FILES);

            $picture = new File();
            $picture->setMimeType($file[ 'type' ][ 'pictureUpload' ])
                ->setExtension(
                    strtolower(
                        substr(
                            $file[ 'name' ][ 'pictureUpload' ],
                            strrpos($file[ 'name' ][ 'pictureUpload' ], '.') + 1
                        )
                    )
                );

            $em = $this->getDoctrine()->getManager();
            $em->persist($picture);
            $em->flush();

            $dir = $this->container->get('kernel')->getProjectDir().'/public/uploads/';

            if (!is_dir($dir)) {
                mkdir($dir);
            }

            move_uploaded_file(
                $file[ 'tmp_name' ][ 'pictureUpload' ],
                $dir.$picture->getId().'.'.$picture->getExtension()
            );

            $user->setPicture($picture);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Erfolgreich registriert. Bitte loggen Sie sich jetzt ein.');

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'user/register.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'user/login.html.twig',
            array(
                'lastUsername' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * @Route("/profile/{id}", name="profile", requirements={"id"="\d*"}, defaults={"id"="0"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function profile(int $id = 0, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $self = true;

        if ($id > 0 && $id != $user->getId()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->find(User::class, $id);
            $self = false;
            /** @var EnemyRepository $repo */
            $repo = $this->getDoctrine()
                ->getRepository(Enemy::class);
            $enemy_status = $repo->get_enemy_status($this->getUser(), $user);
            switch ($enemy_status) {
                case EnemyRepository::STATUS_EMPTY:
                    $enemy_button_text = 'Als Feind hinzufügen';
                    $initiator_id = $this->getUser()->getId();
                    $recipient_id = $user->getId();
                    $ajax_action = '/enemy/add';
                    break;
                case EnemyRepository::STATUS_OPEN:
                    $enemy_button_text = 'Feindschaftsanfrage annehmen';
                    $initiator_id = $user->getId();
                    $recipient_id = $this->getUser()->getId();
                    $ajax_action = '/enemy/accept';
                    break;
                case EnemyRepository::STATUS_SEND:
                    $enemy_button_text = 'Feindschaftsanfrage zurückziehen';
                    $initiator_id = $this->getUser()->getId();
                    $recipient_id = $user->getId();
                    $ajax_action = '/enemy/remove';
                    break;
                case EnemyRepository::STATUS_ACCEPTED:
                    $enemy_button_text = 'Als Feind entfernen';
                    $initiator_id = $this->getUser()->getId();
                    $recipient_id = $user->getId();
                    $ajax_action = '/enemy/remove';
                    break;
            }
        }

        return $this->render(
            'user/profile.html.twig',
            array(
                'user' => $user,
                'picture' => $user->getPicturePath(),
                'self' => $self,
                'enemy_button_text' => isset($enemy_button_text) ? $enemy_button_text : false,
                'recipient_id' => isset($recipient_id) ? $recipient_id : false,
                'initiator_id' => isset($initiator_id) ? $initiator_id : false,
                'http_root' => $request->getHttpHost().$request->getBaseUrl(),
                'ajax_action' => isset($ajax_action) ? $ajax_action : false,
            )
        );
    }

    /**
     * @Route("/profile/save", name="profile_save")
     * @param Request $request
     * @return Response
     */
    public function ajaxSave(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $response = '';
        $value = '';

        $userId = $request->request->get('userId');
        $field = $request->request->get('field');

        if ($field !== 'profile-picture') {
            $value = $request->request->get('value');
        }

        /** @var User $user */
        $user = $em->find(User::class, $userId);

        switch ($field) {
            case 'firstname':
                $user->setFirstName($value);
                break;
            case 'lastname':
                $user->setLastName($value);
                break;
            case 'email':
                $user->setEmail($value);
                break;
            case 'birthday':
                $user->setBirthday(new \DateTime($value));
                break;
            case 'profile-picture':
                $file = reset($_FILES);

                $picture = new File();
                $picture->setMimeType($file[ 'type' ])
                    ->setExtension(
                        strtolower(
                            substr(
                                $file[ 'name' ],
                                strrpos($file[ 'name' ], '.') + 1
                            )
                        )
                    );

                $em = $this->getDoctrine()->getManager();
                $em->persist($picture);
                $em->flush();

                $dir = $this->container->get('kernel')->getProjectDir().'/public/uploads/';

                move_uploaded_file(
                    $file[ 'tmp_name' ],
                    $dir.$picture->getId().'.'.$picture->getExtension()
                );

                $current = $user->getPicture();

                if ($current instanceof File) {
                    unlink($dir.$current->getFileName());
                    $em->remove($current);
                }

                $user->setPicture($picture);

                $response = $user->getPicturePath();
        }

        $em->persist($user);
        $em->flush();

        return new Response($response);
    }

    /**
     * @Route("/profile/enemies", name="profile_enemies")
     * @return Response
     */
    public function ajaxEnemies()
    {
        $user = $this->getUser();
        $enemies = $this->getDoctrine()
            ->getRepository(Enemy::class)
            ->findBy(['initiator' => $user, 'isAccepted' => true]);

        $templates = [];
        /** @var Enemy $enemy */
        foreach ($enemies as $enemy) {
            $templates[] = $this->render(
                'user/enemy.html.twig',
                array(
                    'initiator_id' => $user->getId(),
                    'recipient_id' => $enemy->getRecipient()->getId(),
                    'user' => $enemy->getRecipient(),
                )
            )->getContent();
        }

        $enemies = $this->getDoctrine()
            ->getRepository(Enemy::class)
            ->findBy(['recipient' => $user, 'isAccepted' => true]);

        /** @var Enemy $enemy */
        foreach ($enemies as $enemy) {
            $templates[] = $this->render(
                'user/enemy.html.twig',
                array(
                    'initiator_id' => $enemy->getInitiator()->getId(),
                    'recipient_id' => $user->getId(),
                    'user' => $enemy->getInitiator(),
                )
            )->getContent();
        }

        return new Response(implode("", $templates));

    }

    /**
     * @Route("/profile/enemy_requests", name="profile_enemy_requests")
     * @return Response
     */
    public function ajaxEnemyRequests()
    {
        $user = $this->getUser();
        $enemies = $this->getDoctrine()
            ->getRepository(Enemy::class)
            ->findBy(['recipient' => $user, 'isAccepted' => false]);

        $templates = [];
        /** @var Enemy $enemy */
        foreach ($enemies as $enemy) {
            $templates[] = $this->render(
                'user/enemy_request.html.twig',
                array(
                    'initiator_id' => $enemy->getInitiator()->getId(),
                    'recipient_id' => $user->getId(),
                    'user' => $enemy->getInitiator(),
                )
            )->getContent();
        }

        return new Response(implode("", $templates));
    }
}

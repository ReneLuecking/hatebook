<?php

namespace App\Controller;

use App\Entity\PostHate;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\CommentHate;
use App\Entity\Comment;
use App\Repository\PostHateRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class PostController
 * @package App\Controller
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function home(Request $request)
    {
        return $this->render(
            'post/home.html.twig',
            array(
                'isUserEntity' => $this->getUser() instanceof User,
                'http_root' => $request->getHttpHost().$request->getBaseUrl(),
            )
        );
    }

    /**
     * @Route("/create_post/", name="create_post")
     * @param Request $request
     * @return Response
     */
    public function create_post(Request $request)
    {
        $text = $request->get('text', "");

        if (!empty($text)) {
            $user = $this->getUser();

            $post = new Post();
            $post->setUser($user);
            $post->setDatetime(new \DateTime());
            $post->setText($text);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
        }

        return new Response();
    }


    /**
     * @Route("/create_comment/", name="create_comment")
     * @param Request $request
     * @return Response
     */
    public function create_comment(Request $request)
    {
        $text = $request->get('text', "");
        $post_id = $request->get('post_id');

        if (!empty($text) && !empty($post_id)) {
            $user = $this->getUser();

            /** @var Post $post */
            $post = $this->getDoctrine()
                ->getRepository(Post::class)
                ->find($post_id);

            if (!$post) {
                throw $this->createNotFoundException('No post found for id '.$post_id);
            }

            $comment = new Comment();
            $comment->setText($text);
            $comment->setDatetime(new \DateTime());
            $comment->setUser($user);
            $comment->setPost($post);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return new Response();
    }


    /**
     * @Route("/post/{post_id}", name="post")
     * @param integer $post_id
     * @param Request $request
     * @return Response
     */
    public function post($post_id, Request $request)
    {
        # $post_id = $request->get('post_id', false);
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($post_id);

        $hates = $this->getDoctrine()
            ->getRepository(PostHate::class)
            ->countByPost($post_id);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id '.$post_id);
        }

        if ($hates === false) {
            throw $this->createNotFoundException('Couldnt load Hates for Post '.$post_id);
        }

        $user = $this->getUser();
        $hate = $this->getDoctrine()
            ->getRepository(PostHate::class)
            ->findBy(['user' => $user, 'post' => $post]);
        $hated = empty($hate) ? false : true;

        $template = $this->render(
            'post/post.html.twig',
            array(
                'content' => $post->getText(),
                'post_id' => $post->getID(),
                'hates' => $hates,
                'author' => $post->getUser()->getFullname(),
                'author_id' => $post->getUser()->getId(),
                'http_root' => $request->getHttpHost().$request->getBaseUrl(),
                'datetime' => $post->getDatetime()->format('d.m.Y H:i'),
                'hated' => $hated ? "true" : "false",
                'hate_button_text' => $hated ? "Hasse ich nicht mehr" : "Hasse ich",
            )
        );

        return $template;
    }

    /**
     * @Route("/next_post_ids/", name="next_post_ids")
     * @param Request $request
     * @return Response
     */
    public function next_post_ids(Request $request)
    {
        if (empty($this->getUser())) {
            return new JsonResponse(json_encode([]));
        }
        $offset = $request->get('offset', 0);
        $search = $request->get('s', '');
        /** @var PostRepository $repository */
        $repository = $this->getDoctrine()->getManager()->getRepository(Post::class);
        $user_id = $this->getUser()->getID();

        return new JsonResponse(json_encode($repository->findNewest($user_id, $offset, 5, $search)));
    }

    /**
     * @Route("/comments/", name="comments")
     * @param Request $request
     * @return Response
     */
    public function comments(Request $request)
    {
        $post_id = $request->get('post_id', false);

        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($post_id);

        if (!$post) {
            throw $this->createNotFoundException('post id '.$post_id.' does not exist');
        }

        $user = $this->getUser();
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['post' => $post], ['datetime' => 'desc']);

        $templates = [];
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $hates = $this->getDoctrine()
                ->getRepository(CommentHate::class)
                ->countByComment($comment->getId());
            $hate = $this->getDoctrine()
                ->getRepository(CommentHate::class)
                ->findBy(['user' => $user, 'comment' => $comment]);
            $hated = empty($hate) ? false : true;
            $templates[] = $this->render(
                'post/comment.html.twig',
                array(
                    'content' => $comment->getText(),
                    'comment_id' => $comment->getID(),
                    'author' => $comment->getUser()->getFullName(),
                    'author_id' => $comment->getUser()->getId(),
                    'http_root' => $request->getHttpHost().$request->getBaseUrl(),
                    'hate_button_text' => $hated ? "Hasse ich nicht mehr" : "Hasse ich",
                    'datetime' => $comment->getDatetime()->format('d.m.Y H:i'),
                    'hates' => $hates,
                    'hated' => $hated ? "true" : "false",
                )
            )->getContent();
        }

        return new JsonResponse(json_encode($templates));
    }


    /**
     * @Route("/create_post_hate/", name="create_post_hate")
     * @param Request $request
     * @return Response
     */
    public function create_post_hate(Request $request)
    {
        $doctrine = $this->getDoctrine();

        /** @var Post $post */
        $post = $doctrine->getRepository(Post::class)
            ->find($request->get('post_id', 0));
        $user = $this->getUser();

        if (!$post) {
            throw $this->createNotFoundException('No post found for id '.$request->get('post_id'));
        }

        if (
        $doctrine->getRepository(PostHate::class)
            ->exists($request->get('post_id'), $user->getId())
        ) {
            return new Response();
        }

        $hate = new PostHate();
        $hate->setPost($post);
        $hate->setUser($user);

        $em = $doctrine->getManager();
        $em->persist($hate);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/delete_post_hate/", name="delete_post_hate")
     * @param Request $request
     * @return Response
     */
    public function delete_post_hate(Request $request)
    {
        $doctrine = $this->getDoctrine();

        $post = $doctrine->getRepository(Post::class)
            ->find($request->get('post_id', 0));
        $user = $this->getUser();

        if (!$post) {
            throw $this->createNotFoundException('No post found for id '.$request->get('post_id'));
        }

        $stmt = $doctrine->getConnection()->prepare(
            "DELETE FROM post_hate WHERE post_id = :post_id AND user_id = :user_id"
        );
        $post_id = $post->getId();
        $user_id = $user->getId();
        $stmt->bindParam('post_id', $post_id);
        $stmt->bindParam('user_id', $user_id);
        $stmt->execute();

        return new Response();
    }


    /**
     * @Route("/create_comment_hate/", name="create_comment_hate")
     * @param Request $request
     * @return Response
     */
    public function create_comment_hate(Request $request)
    {
        $doctrine = $this->getDoctrine();

        /** @var Comment $comment */
        $comment = $doctrine->getRepository(Comment::class)
            ->find($request->get('comment_id', 0));
        $user = $this->getUser();

        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id '.$request->get('comment_id'));
        }

        if (
        $doctrine->getRepository(CommentHate::class)
            ->exists($request->get('comment_id'), $user->getId())
        ) {
            return new Response();
        }

        $hate = new CommentHate();
        $hate->setComment($comment);
        $hate->setUser($user);

        $em = $doctrine->getManager();
        $em->persist($hate);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/delete_comment_hate/", name="delete_comment_hate")
     * @param Request $request
     * @return Response
     */
    public function delete_comment_hate(Request $request)
    {
        $doctrine = $this->getDoctrine();

        $comment = $doctrine->getRepository(Comment::class)
            ->find($request->get('comment_id', 0));
        $user = $this->getUser();

        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id '.$request->get('comment_id'));
        }

        $stmt = $doctrine->getConnection()->prepare(
            "DELETE FROM comment_hate WHERE comment_id = :comment_id AND user_id = :user_id"
        );
        $comment_id = $comment->getId();
        $user_id = $user->getId();
        $stmt->bindParam('comment_id', $comment_id);
        $stmt->bindParam('user_id', $user_id);
        $stmt->execute();

        return new Response();
    }

}

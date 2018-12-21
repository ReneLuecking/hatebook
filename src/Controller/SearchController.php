<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SearchController
 * @package App\Controller
 */
class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $search = $request->query->get('s');

        $search = explode(' ', $search);
        $search = array_map('trim', $search);
        $search = array_filter($search);
        $search = array_map('strtolower', $search);

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $postRepo = $this->getDoctrine()->getRepository(Post::class);

        return $this->render(
            'search/search.html.twig',
            array(
                'posts' => array(),
                'users' => $userRepo->search($search),
                'http_root' => $request->getHttpHost().$request->getBaseUrl(),
            )
        );
    }
}

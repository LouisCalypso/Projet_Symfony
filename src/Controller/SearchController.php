<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\Security;

class SearchController extends AbstractController
{
    
    private $postRepository;
    private $security;
    
    public function __construct(PostRepository $postRepository, Security $security)
    {
        $this->postRepository = $postRepository;
        $this->security = $security;
    }

    /**
     * @Route("/search/{page}", name="search", defaults={"page"=1})
     */
    public function index(Request $req, int $page)
    {
        $query = $req->query->get("q");
        if ($page < 1) $page = 1;

        $user = $this->security->getUser();
        $posts = $this->postRepository->findAllTitle($query, $page, 3); // ici mais on peut mettre autre chose (3 par page là)
        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($posts) / 3), // ne pas oublier de changer ce 3 aussi
            'nomRoute' => 'search',
            'paramsRoute' => array()
        );
        
        return $this->render('search/index.html.twig', [
            'userLoggedIn' => $user,
            'routeName' => 'search',
            'posts' => $posts,
            'pagination' => $pagination,
            'query' => $query,
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\Security;


/**
 * Class SearchController
 * manage the Searchbar feature
 * @package App\Controller

 */
class SearchController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var Security logged user details
     */
    private $security;

    /**
     * SearchController constructor.
     * @param PostRepository $postRepository
     * @param Security $security
     */
    public function __construct(PostRepository $postRepository, Security $security)
    {
        $this->postRepository = $postRepository;
        $this->security = $security;
    }

    /**
     * function index
     * Manage the search request and render the result
     * @param Request $req
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/search/{page}", name="search", defaults={"page"=1})
     */
    public function index(Request $req, int $page)
    {
        $query = $req->query->get("q");
        if ($page < 1) $page = 1;
        $user = $this->security->getUser();
        $posts = $this->postRepository->findAllTitle($query, $page, 3);

        
        return $this->render('search/index.html.twig', [
            'userLoggedIn' => $user,
            'routeName' => 'search',
            'posts' => $posts,
            'query' => $query,
        ]);
    }
}

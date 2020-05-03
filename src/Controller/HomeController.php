<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/{page}", name="home")
     */
    public function index($page)
    {
        $posts = $this->postRepository->findAllPagine($page, 3); // ici mais on peut mettre autre chose (3 par page là)
        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($posts) / 3), // ne pas oublier de changer ce 3 aussi
            'nomRoute' => 'home',
            'paramsRoute' => array()
        );
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'pagination' => $pagination
        ]);
    }
    
    private $postRepository;
    
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
}

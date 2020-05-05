<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="root")
     * @Route("/home/{page}", name="home")
     */
    public function index(int $page = 1)
    {
        if ($page < 1 ) $page = 1;

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

    /**
     * @Route("/home/voteAction/ajaxAction", name="voteAction")
     */
    public function voteAction(Request $request){

        $id = $request->request->get('id');
        $type = $request->request->get('type');
        $post = $this->postRepository->findOneById($id);
        if($type == "up-vote"){
            $post->incrementNbVotes();
        }else{
            $post->decrementNbVotes();
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($post);
        $manager->flush();

        $response = new Response(json_encode(array(
            'nbVote' => $post->getNbVotes()
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }
}

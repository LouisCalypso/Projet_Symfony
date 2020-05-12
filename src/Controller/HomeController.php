<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Vote;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    
    private $postRepository;
    private $security;
    
    public function __construct(PostRepository $postRepository, Security $security)
    {
        $this->postRepository = $postRepository;
        $this->security = $security;
    }
    
    /**
     * @Route("/", name="root")
     * @Route("/home/{page}", name="home", defaults={"page"=1})
     */
    public function index(int $page)
    {
        if ($page < 1 ) $page = 1;
        
        $user = $this->security->getUser();
        $posts = $this->postRepository->findAllPagine($page, 3, $user); // ici mais on peut mettre autre chose (3 par page là)
        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($posts) / 3), // ne pas oublier de changer ce 3 aussi
            'nomRoute' => 'home',
            'paramsRoute' => array()
        );
        
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'pagination' => $pagination,
            'userLoggedIn' => $user
        ]);
    }

    /**
     * @Route("/home/voteAction/ajaxAction", name="voteAction")
     */
    public function voteAction(Request $request){
        $id = $request->request->get('id');
        $type = $request->request->get('type');
        $post = $this->postRepository->findOneById($id);
        $manager = $this->getDoctrine()->getManager();
        $user = $this->security->getUser();
        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);
        foreach($post->getVotes() as $voteVar) {
            if($voteVar->getUser()->getId() == $user->getId()) {
                $post->removeVote($voteVar);
                $manager->remove($voteVar);
            }
        }
        if($type == "up-vote"){
            $vote->setType(1);
            $post->incrementNbVotes($vote);
        }else{
            $vote->setType(0);
            $post->decrementNbVotes($vote);
        }
        $user->addVote($vote);
        
        $manager->persist($post);
        $manager->persist($vote);
        $manager->flush();

        $response = new Response(json_encode(array(
            'nbVote' => $post->getNbVotes()
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }
}

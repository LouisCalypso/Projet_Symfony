<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Vote;
use Symfony\Component\Security\Core\Security;


/**
 * Class HomeController
 * Control the post-list and update it
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    
    private $postRepository;
    private $security;
    private $nbPostsPerPages;
    
    public function __construct(PostRepository $postRepository, Security $security)
    {
        $this->postRepository = $postRepository;
        $this->security = $security;
        $this->nbPostsPerPages = 3;
    }
    
    /**
     * Initiate the posts list and render it following registered preferences
     *  (by default : 3 posts per page, page 1, sorted by best posts)
     * @param Request $request
     * @param int $page
     * @return Response
     * @Route("/", name="root")
     * @Route("/home/{page}", name="home", defaults={"page"=1})
     */
    public function index(Request $request, $page = 1)
    {

        if ($page < 1 ) $page = 1;
        $user = $this->security->getUser();
        $postsPerPage = $request->query->get('postsPerPage') ? $request->query->get('postsPerPage') : 3;
        $sortType = $request->query->get('sortType') ? $request->query->get('sortType') : "best-posts";

        $posts = $this->postRepository->findAllPagineSorted($page, $postsPerPage, $sortType);
        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($posts) / $postsPerPage),
            'nomRoute' => 'home',
            'paramsRoute' => array(),

        );
        
        return $this->render('home/index.html.twig', [
            'userLoggedIn' => $user,
            'routeName' => 'home',
            'sortType' => $sortType,
            'posts' => $posts,
            'pagination' => $pagination,
            'postsPerPage' => $postsPerPage

        ]);
    }

    /**
     * AJAX Action
     * up or downvote a post from the posts-list
     * @param Request $request
     * @return Response
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
                if($voteVar->getType() == 1){
                    $post->decrementNbVotes();
                }else{
                    $post->incrementNbVotes();
                }
                $post->removeVote($voteVar);
                $manager->remove($voteVar);
            }
        }
        if($type == "up-vote"){
            $vote->setType(1);
            $post->incrementNbVotes();
            $post->addVote($vote);
        }else{
            $vote->setType(0);
            $post->decrementNbVotes();
            $post->addVote($vote);
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


    /**
     *
     * @param Request $request
     * @return Response
     * @Route("/home/updateAction/ajaxAction", name="updateAction")
     */
    public function updatePostsList(Request $request) {
        $user = $this->security->getUser();

        $page = (int) $request->request->get('page');
        $postsPerPage = abs((int) $request->request->get('postsPerPage'));
        $type =   $request->request->get('type');

        $this->nbPostsPerPages = $postsPerPage;
        if($postsPerPage != null) {
            $this->nbPostsPerPages = $postsPerPage;
        } else {
            $postsPerPage = $this->nbPostsPerPages;

        }
        $posts = $this->postRepository->findAllPagineSorted($page, $postsPerPage, $type);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($posts) / $postsPerPage),
            'nomRoute' => 'home',
            'paramsRoute' => array(),

        );

        $newRender =  $this->render('post/posts-list.html.twig', [
            'userLoggedIn' => $user,
            'sortType' => $type,
            'routeName' => 'home',
            'posts' => $posts,
            'pagination' => $pagination,
            'postsPerPage' => $postsPerPage

        ])->getContent();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($newRender));
        return $response;
    }
}

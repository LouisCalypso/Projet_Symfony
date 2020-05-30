<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;

/**
 * Class PostController
 * Manage post and his comments
 * Post and Comment display
 * Post Creation an Deletion
 * Comment Creation
 *
 * @package App\Controller
 */
class PostController extends AbstractController
{

    /**
     * @var Security logged user details
     */
    private $security;

    /**
     * @var PostRepository post db queries
     */
    private $postRepository;

    /**
     * PostController constructor.
     * @param Security $security
     * @param PostRepository $postRepository
     */
    public function __construct(Security $security, PostRepository $postRepository )
    {
        $this->security = $security;
        $this->postRepository = $postRepository;
    }


    /**
     * function show
     * Initiate post and get his comment
     * Initiate comment form and create comments
     * @param Post $post selected post
     * @param Request $request
     * @param MailerInterface $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/posts/{id}", name="post")
     */

    public function show(Post $post, Request $request, MailerInterface $mailer)
    {

        $user = $this->security->getUser();

        //TODO : COMMENT POST
        $commentForm = $this->createForm(CommentType::class);

        // the form takes the request
        // and will retrieve the fields filled by the HTML form
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // We get the Comment object created by the form
            /** @var Comment $comment */
            $comment = $commentForm->getData();

            // The comment is associated with the article and the creation date is defined
            $comment
                ->setPost($post)
                ->setUser($this->getUser())
                ->setCreatedAt(new \DateTime());

            // we get the EntityManager of Doctrine which will be used to save our comment in the database
            $manager = $this->getDoctrine()->getManager();

            // the persist says to Doctrine to consider this Object as an object to save in base
            // the object is now managed by Doctrine
            $manager->persist($comment);
            //
            // flush tells Doctrine to execute SQL queries to create / modify objects sur lesquels
            // on which we called persist ()
            $manager->flush();

            // If it's not the post owner
            //If it is not the owner of the post who comments on his post
            /*if($post->getUser()->getEmail() != $this->getUser()->getEmail()){
                // New Comment mail
               $email = (new Email())
                    ->from('ig2i@symfodoggos.com')
                    ->to($post->getUser()->getEmail()) //->to('louis.duretete@gmail.com')
                    ->subject("Someone commented on your post : ".$post->getTitle()." !")
                    ->html("<p>".$this->getUser()->getUsername()." commented on your post : <b>".$post->getTitle()." </b>!</p>
                                    <p>You can see the comment here : http://localhost:8080/posts/".$post->getId()."</p>");
                $mailer->send($email);
            }*/

            // redirect to the actual page
            // redirection prevents the form from being returned
            return $this->redirectToRoute('post', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            'userLoggedIn' => $user,
            'routeName' => 'post',
            'post' => $post,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * function createAction
     * AJAX ACTION
     * ajax function available in /public/js/script.js
     *      =>  $(document).on('click','.modal-trigger',function ()
     *
     * Initiate Post Creation Form
     * Create Post following user choice :
     * Image, text or Link Post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/post/createAction", name="post/create")
     */

    public function createAction(Request $request)
    {
        $type = $request->query->get('type');
        $user = $this->getUser();

        $postForm = $this->createForm(PostType::class, null, ['type' => $type]);
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            // we get the object created by the form
            $post = $postForm->getData();
            $post
                ->setNbVotes(0)
                ->setCreatedAt(new \DateTime())
                ->setUser($user);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('post', ['id' => $post->getId()]);
        }

        return $this->render('post/creation.html.twig', [
            'userLoggedIn' => $user,
            'routeName' => 'post/create',
            'postForm' => $postForm->createView(),
            'formType' => $type,
        ]);
    }

    /**
     * function deleteAction
     * AJAX ACTION
     * ajax function available in /public/js/script.js
     *      =>  $(".delete-post").click(function ()
     *
     * delete the selected post
     * @param Request $request
     * @return Response
     * @Route("/posts/deleteAction/ajaxAction", name="post/delete")
     */
    public function deleteAction(Request $request){
        $res = new Response();
        $id = $request->request->get('id');

        $post = $this->postRepository->findOneById($id);

        if ($post == null) {
            $res->setStatusCode(404);
            return $res;
        }

        $manager = $this->getDoctrine()->getManager();
        foreach ($post->getVotes() as $vote)
            $manager->remove($vote);
        foreach ($post->getComments() as $com)
            $manager->remove($com);    
        $manager->remove($post);
        $manager->flush();

        $res->setStatusCode(204);
        $res->headers->set('Content-Type', 'application/json');
        return $res;

    }
}

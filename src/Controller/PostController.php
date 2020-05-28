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

class PostController extends AbstractController
{

    private $security;
    private $postRepository;

    public function __construct(Security $security, PostRepository $postRepository )
    {
        $this->security = $security;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/posts/{id}", name="post")
     */
    public function show(Post $post, Request $request, MailerInterface $mailer)
    {

        $user = $this->security->getUser();

        //TODO : COMMENT POST
        $commentForm = $this->createForm(CommentType::class);
        // le formulaire prend la requête et va récupérer à lintérieur les champs
        // remplis par le formulaire HTML
        $commentForm->handleRequest($request);

        // Si le formulaire a été soumis et est valide
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // on récupère l'objet Comment créé par le formulaire
            /** @var Comment $comment */
            $comment = $commentForm->getData();

            // on associe le commentaire à l'article et on défini la date de création
            $comment
                ->setPost($post)
                ->setUser($this->getUser())
                ->setCreatedAt(new \DateTime());

            // on recupère le l'EntityManager de Doctrine qui va nous servir à sauvegarder notre commentaire en base de données
            $manager = $this->getDoctrine()->getManager();

            // le persist dit a Doctrine de conidérer cet Objet comme un objet à sauvegarder en base, l'objet est donc maintenant
            // géré par Doctrine
            $manager->persist($comment);
            // le flush dit à Doctrine d'exécuter les requêtes SQL permettant de créer/modifier les objets sur lesquels
            // on appelé ->persist()
            $manager->flush();

            //S'il ne s'agit pas du propriétaire du poste qui commente son poste
            if($post->getUser()->getEmail() != $this->getUser()->getEmail()){
                // Mail de confirmation d'inscritpion
               $email = (new Email())
                    ->from('ig2i@symfodoggos.com')
                    ->to($post->getUser()->getEmail()) //->to('louis.duretete@gmail.com')
                    ->subject("Quelqu'un a commenté votre poste : ".$post->getTitle()." !")
                    ->html("<p>".$this->getUser()->getUsername()." a commenté votre poste : <b>".$post->getTitle()." </b>!</p>
                                    <p>Vous pouvez voir ce qu'il/elle a dit avec ce lien : http://localhost:8080/posts/".$post->getId()."</p>");
                $mailer->send($email);
            }

            // redirige vers la page actuelle (la redirection permet d'éviter qu'en actualisant la page, cela soumette
            // à nouveau le formulaire
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
     * @Route("/post/createAction", name="post/create")
     */
    public function createAction(Request $request)
    {
        $type = $request->query->get('type');
        $user = $this->getUser();

        $postForm = $this->createForm(PostType::class, null, ['type' => $type]);
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            // on récupère l'objet Comment créé par le formulaire
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

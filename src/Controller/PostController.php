<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/posts/{id}", name="post")
     */
    public function show(Post $post, Request $request)
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

            // redirige vers la page actuelle (la redirection permet d'éviter qu'en actualisant la page, cela soumette
            // à nouveau le formulaire
            return $this->redirectToRoute('post', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            //'controller_name' => 'PostController',
            'post' => $post,
            'commentForm' => $commentForm->createView(),
            'userLoggedIn' => $user

        ]);
    }
}

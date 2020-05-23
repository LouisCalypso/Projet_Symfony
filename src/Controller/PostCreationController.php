<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostCreationController extends AbstractController
{
    /**
     * @Route("/post/creation", name="create-post")
     */
    public function index(Request $request)
    {

        $postForm = $this->createForm(PostType::class);
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            // on récupère l'objet Comment créé par le formulaire
            /** @var Comment $comment */
            $post = $postForm->getData();
            $post
                ->setNbVotes(0)
                ->setCreatedAt(new \DateTime())
                ->setUser($this->getUser())
                ->setLink(null);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('post', ['id' => $post->getId()]);
        }

        return $this->render('post-creation/post-creation.html.twig', [
            'controller_name' => 'PostCreationController',
            'postForm' => $postForm->createView(),

        ]);
    }
}

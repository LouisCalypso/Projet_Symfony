<?php

namespace App\Controller;

use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class RegisterController extends AbstractController
{
    private $encoder;
    private $security;

    public function __construct(UserPasswordEncoderInterface $encoder, Security $security)
    {
        $this->encoder = $encoder;
        $this->security = $security;
    }

    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $userFormType = $this->createForm(UserFormType::class);

        // le formulaire prend la requête et va récupérer à lintérieur les champs remplis par le formulaire HTML
        $userFormType->handleRequest($request);

        // Si le formulaire a été soumis et est valide
        if ($userFormType->isSubmitted() && $userFormType->isValid()) {
            // on récupère l'objet User créé par le formulaire
            /** @var User $user */
            $user = $userFormType->getData();

            // on défini la date de création
            $user
                ->setCreatedAt(new \DateTime())
                ->setPassword($this->encoder->encodePassword($user, $userFormType->getData()->getPassword()));

            // on recupère le l'EntityManager de Doctrine qui va nous servir à sauvegarder notre user en base de données
            $manager = $this->getDoctrine()->getManager();

            // le persist dit a Doctrine de conidérer cet Objet comme un objet à sauvegarder en base, l'objet est donc maintenant
            // géré par Doctrine
            $manager->persist($user);
            // le flush dit à Doctrine d'exécuter les requêtes SQL permettant de créer/modifier les objets sur lesquels
            // on appelé ->persist()
            $manager->flush();

            // Mail de confirmation d'inscritpion
            $email = (new Email())
                ->from('inscription@symfodoggos.com')
                ->to($userFormType->getData()->getEmail()) //->to('louis.duretete@gmail.com')
                ->subject('Bienvenue sur SymfoDoggos!')
                ->html("<p>Nous vous confirmons l'inscritpion sur symfodoggos.com !</p><p>http://localhost:8080/</p>");

            $mailer->send($email);

            // redirige vers la page actuelle (la redirection permet d'éviter qu'en actualisant la page, cela soumette
            // à nouveau le formulaire
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'userLoggedIn' => $this->security->getUser(),
            'routeName' => 'register',
            'userFormType' => $userFormType->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class RegisterController
 * Control user registration
 * @package App\Controller

 */
class RegisterController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var Security
     */
    private $security;

    /**
     * RegisterController constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param Security $security
     */
    public function __construct(UserPasswordEncoderInterface $encoder, Security $security)
    {
        $this->encoder = $encoder;
        $this->security = $security;
    }


    /**
     * function index
     * Initiate User registration form and create user
     * @param Request $request
     * @param MailerInterface $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("/register", name="register")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $userFormType = $this->createForm(UserFormType::class);

        // the form takes the request
        // and will retrieve the fields filled by the HTML form
        $userFormType->handleRequest($request);

        if ($userFormType->isSubmitted() && $userFormType->isValid()) {
            // We get the user object created by the form
            /** @var User $user */
            $user = $userFormType->getData();

            $user
                ->setCreatedAt(new \DateTime())
                ->setPassword($this->encoder->encodePassword($user, $userFormType->getData()->getPassword()));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            // Registration confirmation email
            $email = (new Email())
                ->from('inscription@symfodoggos.com')
                ->to($userFormType->getData()->getEmail()) //->to('louis.duretete@gmail.com')
                ->subject('Welcome to SymfoDoggos !')
                ->html("<p>You are now registered on symfodoggos.com !</p><p>http://localhost:8080/</p> <h4>Who let the dogs out ?</h4>");

            $mailer->send($email);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'userLoggedIn' => $this->security->getUser(),
            'routeName' => 'register',
            'userFormType' => $userFormType->createView(),
        ]);
    }
}

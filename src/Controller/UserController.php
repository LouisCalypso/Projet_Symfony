<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{

    private $userRepository;
    private $security;
    
    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function me()
    {
        // if no login redirect to login
        if(($user = $this->security->getUser()) == null)
            return $this->redirectToRoute('app_login');

        return $this->render('user/me.html.twig',[
            'userLoggedIn' => $user,
            'routeName' => 'post',
        ]);
    }

    /**
     * @Route("/users/{id}", name="users")
     */
    public function him(int $id)
    {
        // if no user id redirect to root
        if($id == null)
            return $this->redirectToRoute('root');

        $userVisited = $this->userRepository->find($id);
        return $this->render('user/him.html.twig',[
            'userLoggedIn' => $this->security->getUser(),
            'routeName' => 'post',
            'userVisited'=>$userVisited
        ]);
    }

}

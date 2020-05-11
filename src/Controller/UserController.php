<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function me()
    {
        // if no login redirect to login
        if($this->getUser() == null)
            return $this->redirectToRoute('app_login');

        return $this->render('user/me.html.twig');
    }

    /**
     * @Route("/users/{id}", name="users")
     */
    public function him(int $id)
    {
        // if no user id redirect to root
        if($id == null)
            return $this->redirectToRoute('root');

        $user = $this->userRepository->find($id);
        return $this->render('user/him.html.twig',['user'=>$user]);
    }
    
    private $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}

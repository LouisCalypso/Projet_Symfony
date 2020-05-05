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
        // if no login redirect to root
        return $this->render('user/me.html.twig');
    }

    /**
     * @Route("/users/{id}", name="users")
     */
    public function him(int $id)
    {
        // if no user id redirect to root
        return $this->render('user/him.html.twig',['id'=>$id]);
    }
    
    private $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}

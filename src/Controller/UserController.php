<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserController
 * Control user profile mage (connected user and other users)
 * @package App\Controller
 */
class UserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Security
     */
    private $security;


    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param Security $security
     */
    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }
    
    /**
     * function me
     * render user profile if user is conencted
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/profile", name="profile")
     */

    public function me()
    {
        // if no login redirect to login
        if(($user = $this->security->getUser()) == null)
            return $this->redirectToRoute('app_login');
        
        $user = $this->userRepository->find($user->getId());

        return $this->render('user/me.html.twig',[
            'sortType' => 'older',
            'postsPerPage' => 1,
            'userLoggedIn' => $user,
            'routeName' => 'profile',
        ]);
    }
    
    /**
     * function him
     * render selected user profile
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/users/{id}", name="users")
     */

    public function him(int $id)
    {
        // if no user id redirect to root
        if($id == null)
            return $this->redirectToRoute('root');
        
        $userVisited = $this->userRepository->find($id);
        $userLoggedIn = $this->security->getUser();

        if($userLoggedIn && $userLoggedIn->getId() == $id)
            return $this->me();


        return $this->render('user/him.html.twig',[
            'sortType' => 'older',
            'userLoggedIn' => $userLoggedIn,
            'postsPerPage' => 1,
            'routeName' => 'users',
            'userVisited'=> $userVisited
        ]);
    }
        
}
    
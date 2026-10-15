<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Command\UserPasswordHashCommand;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{   
    #[Route(path: "/", name: "home")]
    public function index(Request $request, UserPasswordHasherInterface $hasher,  EntityManagerInterface $em, Security $security): Response {

        // $user = new User();
        // $user->setEmail("henry.com")
        //     ->setUsername("henry")
        //     ->setPassword($hasher->hashPassword($user, "henry"))
        //     ->setRoles([]);
        
        // $em->persist($user);
        // $em->flush();
        // dd($this->getUser());
        // dd($this->getUser());
        // dd($security->getUser());
        // dd($security->getToken());


        return $this->render("home/index.html.twig");
    }
}




<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // $user = new User();
        // $user->setEmail('admin@gmail.com')
        //     ->setUsername('Christ MPASSI')
        //     ->setPassword($hasher->hashPassword($user, 'Emiliodave'))
        //     ->setRoles([]);
        //     $em->persist($user);
        //     $em->flush();

        return $this->render('home/index.html.twig');

        // return new Response('Bonjour'.$_GET['name']);
    }
}

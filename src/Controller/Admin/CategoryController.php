<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;



#[Route("/category", name:'category.')]
class CategoryController extends AbstractController
{

    #[Route(name:'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories'=> $categoryRepository->findAll()
        ]);
    }

    #[Route("/create", name:'create')]
    public function create(Request $request, EntityManagerInterface $em)
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été créée');
            return $this->redirectToRoute('category.index');
        }
        return $this->render('category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/{id}/edit", name:'edit', requirements: ['id' => Requirement::DIGITS], methods:['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée');
            return $this->redirectToRoute('category.index');
        }
        return $this->render('category/edit.html.twig', [
            'category'=> $category,
            'form' => $form
        ]);
    }

    #[Route("/{id}", name:'delete', requirements: ['id' => Requirement::DIGITS], methods:['DELETE'])]
    public function remove(Category $category, EntityManagerInterface $em)
    {
        
        $em->remove($category);
        $em->flush();
        $this->addFlash('success','La catégorie  a bien été supprimée');
        return $this->redirectToRoute('category.index');
    }
}

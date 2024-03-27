<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[IsGranted('ROLE_ADMIN')]
class RecipeController extends AbstractController
{
    #[Route('/recipe', name: "recipe.index")]
    public function index(RecipeRepository $recipeRepository, Request $request): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $page = $request->query->getInt('page',1);
        $limit = 2;
        $recipes = $recipeRepository->paginateRecipes($page, $limit);
        $maxPage = ceil($recipes->count() / $limit);
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'maxPage' => $maxPage,
            'page'=> $page
        ]);
        //$recipes = $recipeRepository->findAll();

        // $recipe = new Recipe();
        // $recipe->setTitle('Burger royal');
        // $recipe->setSlug('burger-royal');
        // $em->persist($recipe);
        // $em->flush();


    }

    #[Route('/recipe/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(string $slug, string $id, RecipeRepository $recipeRepository): Response
    {
        $recipe = $recipeRepository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('repice.show', [
                'slug' => $recipe->getSlug(), 'id' => $recipe->getId()
            ]);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/recipes/{id}/edit', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper)
    {

        // recuperer le chemin du ficher 
        // dd($helper->asset($recipe, 'thumbnailFile'));


        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // $recipe->setUpdateddAt(new \DateTimeImmutable());
            
            /** @var UploadedFile $file */
            $file = $form->get('thumbnailFile')->getData();
            $fileName = $recipe->getSlug() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kermel.project_dir') . '/public/asset/images/recipes', $fileName);
            $recipe->setThumbnail($fileName);

            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    #[Route('/recipe/create', name: 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // $recipe->setCreatedAt(new \DateTimeImmutable());
            // $recipe->setUpdatedAt(new \DateTimeImmutable());

            /** @var UploadedFile $file */
            $file = $form->get('thumbnailFile')->getData();
            $fileName = $recipe->getSlug() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kermel.project_dir') . '/public/asset/images/recipes', $fileName);
            $recipe->setThumbnail($fileName);
            $em->persist($recipe);
            $file->getClientOriginalName();
            
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/recipe/{id}/edit', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipe.index');
    }
}

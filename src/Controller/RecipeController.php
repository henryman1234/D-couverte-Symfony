<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController  {

    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request , RecipeRepository $repository, EntityManagerInterface $em): Response { 

        // $recipes = $em->getRepository(Recipe::class)->findWithDurationLowerThan(20);
        $recipes = $repository->findAll();

        // $recipe = new Recipe()
        //     ->setTitle("L'okok avec le batton de manioc")
        //     ->setSlug("okok-sucré")
        //     ->setContent("Ceci est la duréé de okok sucré")
        //     ->setCreatedAt(new DateTimeImmutable())
        //     ->setDuration(30)
        //     ->setUpdateAt(new DateTimeImmutable());

        // $em->persist($recipe);
        // $em->remove($recipes[0]);
        // $em->flush();

        return $this->render('recipe/index.html.twig', [
            "recipes" => $recipes
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ["slug" => "[a-z0-9\-]+", "id" => "\d+"])]
    public function show(Request $request, string $slug , int $id, RecipeRepository $repository): Response {
        
        $recipe = $repository->findOneBy(["id" => $id]);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute("recipe.show", ["id" => $recipe->getId(), "slug" => $recipe->getSlug()]);
        }
        return $this->render("recipe/show.html.twig", [
            'recipe' => $recipe,
        ]);
        
    }

    #[Route("/recettes/{id}/edit", name: "recipe.edit",methods: ["GET", "POST"])]
    public function edit (Recipe $recipe, RecipeRepository $repository, Request  $request,  EntityManagerInterface  $em) {

        $form = $this->createForm(RecipeType::class,  $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted()  &&  $form->isValid()) {
            // $recipe->setUpdateAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash("success", "La recette a bien été modifiée");
            return $this->redirectToRoute("recipe.index");
        }

        return $this->render("recipe/edit.html.twig", [
            "recipe" =>$recipe,
            "form" => $form
        ]);
    }

    #[Route("/recettes/create", name: "recipe.create")]
    public function create (Request $request, EntityManagerInterface $em) {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdateAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash("success", "La recette a bien été créee");
            return $this->redirectToRoute("recipe.index");
        }

        return $this->render("recipe/create.html.twig", [
            "recipe" => $recipe,
            "form" => $form
        ]);
    }


    #[Route("/recettes/{id}", name: "recipe.delete", methods: ["DELETE"])]
    public function delete (Request $request, Recipe $recipe, EntityManagerInterface $em) {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash("success", "la recette a bien été supprimé");
        return $this->redirectToRoute("recipe.index");
    } 
}

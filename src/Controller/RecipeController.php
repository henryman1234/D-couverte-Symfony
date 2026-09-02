<?php

namespace App\Controller;

use App\Demo;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RecipeController extends AbstractController
{

    #[Route("/demo")]
    public function demo (Demo $demo) {
        
        return new Response("Découverte des services");
    }


    #[Route(path: "/recettes", name: "recipe.index")]
    public function index (Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response {

        // $resultTime = $repository->findTotalDuration()[0];
        // $duration = $resultTime["total"];

        $recipes = $em->getRepository(Recipe::class)->findAll();

        // $em->remove($recipes[3]);

        // $em->flush();
        // $recipes =  $repository->findWithDurationLowerThan(10);

        return $this->render("recipe/index.html.twig",  [
            "recipes" => $recipes,
        ]);
    }


    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements:["slug" => '[a-z0-9-]+', "id" => "\d+"])]
    public function show(Request $request, string $slug, int $id,  RecipeRepository $repository): Response {

        $recipe =  $repository->findOneBy(["id" =>  $id]);
        if ($recipe->getSlug()  !==  $slug) {
            return $this->redirectToRoute("recipe.show", ["slug" => $recipe->getSlug() , "id" => $recipe->getId()]);
        }

        return $this->render("recipe/show.html.twig", [
            "recipe" =>  $recipe,
            "slug" => $recipe->getSlug()
        ]);
    }

    #[Route(path: "/recettes/{id}/edit", name: "recipe.edit", requirements: ["id" => "\d+"])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {

        $form = $this->createForm(RecipeType::class, $recipe);
        // $form = $formFactory->create(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() &&  $form->isValid()) {
            // $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash("success", "La recette a bien été modifiée");
            return $this->redirectToRoute('recipe.index');
        }
        
        return $this->render("recipe/edit.html.twig", [
            "recipe" => $recipe,
            "form" => $form
        ]);
    }

    #[Route("/recettes/create", name:"recipe.create")]
    public function create (Request $request, EntityManagerInterface $em) {
        
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() &&  $form->isValid()) {
            // $recipe->setCreatedAt(new DateTimeImmutable());
            // $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash("success", "La recette a bien été créee");
            return $this->redirectToRoute("recipe.index");
        }

        return $this->render("recipe/create.html.twig", [
            "form" => $form
        ]);
    }

    #[Route("/recettes/{id}", name: 'recipe.delete', methods: ["DELETE"], requirements:["id" => "\d+"])]
    public function delete (Recipe $recipe, EntityManagerInterface $em) {
        
        $em->remove($recipe);
        $em->flush();
        $this->addFlash("success", "La recette a été supprimée");
        return $this->redirectToRoute("recipe.index");
    }
}




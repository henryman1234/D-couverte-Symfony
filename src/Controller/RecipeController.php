<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route(path: "/recettes", name: "recipe.index")]
    public function index (Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response {

        dd($repository->findAll());
        // $resultTime = $repository->findTotalDuration()[0];
        // $duration = $resultTime["total"];

        $recipes = $em->getRepository(Recipe::class)->findAll();

        // $em->remove($recipes[3]);

        // $em->flush();
        // $recipes =  $repository->findWithDurationLowerThan(10);

        return $this->render("recipe/index.html.twig",  [
            "recipes" => $recipes,
            "total" => $duration
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
}

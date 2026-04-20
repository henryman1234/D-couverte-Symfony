<?php

namespace App\Controller;

use App\Entity\Recipe;
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

        $recipes = $em->getRepository(Recipe::class)->findWithDurationLowerThan(20);

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
}

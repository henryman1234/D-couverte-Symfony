<?php

namespace App\Controller\Admin;

use App\Demo;
use App\Entity\Category;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: "/admin/recettes", name: "admin.recipe.")]
final class RecipeController extends AbstractController
{

    #[Route("/demo")]
    public function demo (Demo $demo) {
        
        return new Response("Découverte des services");
    }


    #[Route(path: "/", name: "index")]
    public function index (Request $request, RecipeRepository $repository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response {

        $plat = $repository->findOneBy(["slug" =>  "patte-bolognaise"]);

        
        $recipes = $em->getRepository(Recipe::class)->findAll();

        $category = (new Category())
            ->setUpdatedAt(new DateTimeImmutable())
            ->setCreatedAt(new DateTimeImmutable())
            ->setName("Demo")
            ->setSlug("demo");
        
        $em->persist($category);
        $recipes[0]->setCategory($category);

        // $recipes = $repository->test(500);
        // dd($recipes[3]->getCategory()->getName());

        // $em->remove($recipes[3]);

        // $em->flush();
        // $recipes =  $repository->findWithDurationLowerThan(10);

        return $this->render("admin/recipe/index.html.twig",  [
            "recipes" => $recipes,
        ]);
    }

    #[Route("/create", name:"create")]
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
            return $this->redirectToRoute("admin.recipe.index");
        }

        return $this->render("admin/recipe/create.html.twig", [
            "form" => $form
        ]);
    }


    #[Route('/{slug}-{id}', name: 'show', requirements:["slug" => '[a-z0-9-]+', "id" => "\d+"])]
    public function show(Request $request, string $slug, int $id,  RecipeRepository $repository): Response {

        $recipe =  $repository->findOneBy(["id" =>  $id]);
        if ($recipe->getSlug()  !==  $slug) {
            return $this->redirectToRoute("admin.recipe.show", ["slug" => $recipe->getSlug() , "id" => $recipe->getId()]);
        }

        return $this->render("admin/recipe/show.html.twig", [
            "recipe" =>  $recipe,
            "slug" => $recipe->getSlug()
        ]);
    }


    #[Route(path: "/{id}/edit", name: "edit", requirements: ["id" => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {

        $form = $this->createForm(RecipeType::class, $recipe);
        // $form = $formFactory->create(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() &&  $form->isValid()) {
            // $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash("success", "La recette a bien été modifiée");
            return $this->redirectToRoute('admin.recipe.index');
        }
        
        return $this->render("admin/recipe/edit.html.twig", [
            "recipe" => $recipe,
            "form" => $form
        ]);
    }



    #[Route("/{id}/delete", name: 'delete', methods: ["DELETE"], requirements:["id" => "\d+"])]
    public function delete (Recipe $recipe, EntityManagerInterface $em) {
        
        $em->remove($recipe);
        $em->flush();
        $this->addFlash("success", "La recette a été supprimée");
        return $this->redirectToRoute("admin.recipe.index");
    }
}




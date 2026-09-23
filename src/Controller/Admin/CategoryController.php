<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route(path: "/admin/category", name: "admin.category.")]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            "categories" => $repository->findAll()
        ]);
    }
    
    #[Route(path: "/create",  name: "create")]
    public function create (Request $request, EntityManagerInterface $em) {
        
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() &&  $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash("success", "La catégorie a été créee");
            return $this->redirectToRoute("admin.category.index");
        }

        return $this->render("admin/category/create.html.twig", [
            "form" => $form
        ]);
    

    } 

    #[Route(path:"/edit/{id}",  name:"edit", requirements: ["id" => Requirement::DIGITS])]
    public function edit (Category $category, Request $request, EntityManagerInterface  $em) {
        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() &&  $form->isValid()) {
            $em->flush();
            $this->addFlash("success", "La catégorie a été modifiée");
            return $this->redirectToRoute("admin.category.index");
        }

        return $this->render("admin/category/edit.html.twig", [
            "form" => $form,
            "category" =>  $category
        ]);
    } 

    #[Route(path:"/delete/{id}", name: "delete", requirements: ["id" => "\d+"])]
    public function delete (Request $request, Category $category, EntityManagerInterface $em) {
        $em->remove($category);
        $em->flush();
        $this->addFlash("success", "La catégorie a bien été supprimé");
        return $this->redirectToRoute("admin.category.index");

    } 
}

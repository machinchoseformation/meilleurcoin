<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{





    /**
     * @Route("/admin/category/", name="admin_list_category")
     */
    public function listCategory()
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findAll();

        return $this->render("admin/list_category.html.twig", [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/admin/category/detail/{id}", name="admin_detail_category")
     */
    public function detailCategory(Category $category)
    {
        return $this->render("admin/detail_category.html.twig", [
            "category" => $category
        ]);
    }

    /**
     * @Route("/admin/category/remove/{id}", name="admin_remove_category")
     */
    public function removeCategory(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash("success", "Categorie supprimée !");
        return $this->redirectToRoute("admin_list_category");
    }

    /**
     * @Route("/admin/category/update/{id}", name="admin_update_category")
     */
    public function updateCategory(Category $category, Request $request)
    {
        //on associe une entité provenant de la bdd à notre form
        //c'est ce qui fait que le form est prérempli
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "Catégorie mise à jour!");
            return $this->redirectToRoute("admin_detail_category", ["id" => $category->getId()]);
        }

        return $this->render("admin/update_category.html.twig", [
            "form" => $form->createView(),
            "category" => $category
        ]);
    }

    /**
     * @Route("/admin/category/create/", name="admin_create_category")
     */
    public function createCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "Catégorie sauvegardée!");
            return $this->redirectToRoute("admin_detail_category", ["id" => $category->getId()]);
        }

        return $this->render("admin/create_category.html.twig", [
            "form" => $form->createView()
        ]);
    }
}












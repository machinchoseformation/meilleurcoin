<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        //récupère l'éventuelle id de catégorie / mot-clef présent dans l'URL
        $categoryId = $request->query->get("cat");
        $keyword = $request->query->get("q");

        //récupère le repository des annonces
        $adRepo = $this->getDoctrine()->getRepository(Ad::class);
        //récupère les 50 annonces les plus récentes
        $ads = $adRepo->findHomeAds($categoryId, $keyword);

        //passe les annonces à twig !
        return $this->render("default/home.html.twig", [
            "ads" => $ads,
        ]);
    }

    /**
     * @Route("/foire-aux-questions/", name="faq")
     */
    public function faq()
    {
        return $this->render("default/faq.html.twig");
    }

    /**
     * @Route("/cgu/", name="cgu")
     */
    public function cgu()
    {
        return $this->render("default/cgu.html.twig");
    }


    //cette méthode est appelée depuis le fichier Twig (layout) !!!!
    //voir https://symfony.com/doc/current/templating/embedding_controllers.html
    public function listAll()
    {
        $cateRepo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $cateRepo->findAll();

        return $this->render("default/category_list.html.twig", [
            "categories" => $categories,
            //le fait d'être dans une sous-requête complique les choses
            //pour récupérer le paramètre d'URL normalement
            "categoryId" => $_GET['cat'] ?? null,
        ]);
    }

    /**
     * @Route(
     *     "/test/{id}",
     *     name="test",
     *     defaults={"id": null},
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     *     )
     */
    public function test($id)
    {
        dump($id);
        die();
    }

    /**
     * @Route(
     *     "/test/",
     *     name="test2",
     *     )
     */
    public function test2(EntityManagerInterface $em)
    {
        //ou
        //$em = $this->getDoctrine()->getManager();

        dump("test2");
        die();
    }
}











<?php

namespace App\Controller;

use App\Entity\Ad;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Préfixe toutes les urls par /favoris
 * @Route("/favoris")
 */
class FavoriteController extends Controller
{
    /**
     * @Route("/ajouter/{id}", name="add_favorite")
     */
    public function addToFavorite($id)
    {
        $em = $this->getDoctrine()->getManager();
        $adRepo = $this->getDoctrine()->getRepository(Ad::class);

        $user = $this->getUser();
        $ad = $adRepo->find($id);

        if ($user->getFavorites()->contains($ad)){
            $this->addFlash("danger", "Cette annonce est déjà dans vos favoris!");
            return $this->redirectToRoute("ad_detail", ["id" => $id]);
        }

        $user->addFavorite($ad);
        $em->flush();

        $this->addFlash("success", "Ajoutée !");
        return $this->redirectToRoute("ad_detail", ["id" => $id]);
    }

    /**
     * @Route("/retirer/{id}", name="remove_favorite")
     */
    public function removeFromFavorite($id)
    {
        $em = $this->getDoctrine()->getManager();
        $adRepo = $this->getDoctrine()->getRepository(Ad::class);

        $user = $this->getUser();
        $ad = $adRepo->find($id);

        $user->removeFavorite($ad);
        $em->flush();

        $this->addFlash("success", "Retirée !");
        return $this->redirectToRoute("ad_detail", ["id" => $id]);
    }

    /**
     * @Route("/", name="favorite_list")
     */
    public function list()
    {
        return $this->render("favorite/list.html.twig");
    }

}
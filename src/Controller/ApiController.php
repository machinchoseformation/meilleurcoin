<?php
namespace App\Controller;


use App\Entity\Ad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


class ApiController extends Controller
{
    /**
     * @Route("/api/v1/ads/", methods={"GET"})
     */
    public function api_list_ads()
    {
        $repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();

        //voir la fonction jsonSerialize dans ad.php !

        return $this->json($ads);
    }
}
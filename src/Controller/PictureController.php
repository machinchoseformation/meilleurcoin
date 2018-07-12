<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Picture;
use App\Form\PictureType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PictureController extends Controller
{
    /**
     * @Route("/annonces/depot/image/{id}", name="upload")
     */
    public function upload(Request $request, Ad $ad)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        //on vérifie que c'est bien le créateur de l'annonce qui upload
        if ($this->getUser() !== $ad->getCreator()){
            throw $this->createAccessDeniedException("Pas votre annonce ça!");
        }

        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $picture->getFile();
            $fileName = md5(uniqid()).'.'.$uploadedFile->guessExtension();

            // moves the file to the directory where brochures are stored
            $uploadedFile->move(
                $this->getParameter('upload_dir'),
                $fileName
            );

            $picture->setFilename($fileName);

            $picture->setDateCreated(new \DateTime());
            $picture->setAd($ad);

            $em = $this->getDoctrine()->getManager();
            $em->persist($picture);
            $em->flush();

            $this->addFlash("success", "Image chargée !");
            return $this->redirectToRoute("ad_detail", ["id" => $ad->getId()]);
        }

        return $this->render("picture/upload.html.twig", [
            "form" => $form->createView(),
            "ad" => $ad
        ]);
    }


}
<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Mailer\Mailer;
use App\StringHelpers\Slugifier;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdController extends Controller
{

    /**
     * @Route(
     *     "/annonces/suppression/{id}",
     *     name="ad_delete",
     *     requirements={"id": "\d+"}
     * )
     */
    public function remove($id)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $adRepo = $this->getDoctrine()->getRepository(Ad::class);
        $ad = $adRepo->find($id);

        if ($ad->getCreator() != $this->getUser()){
            throw $this->createAccessDeniedException("Ce n'est pas votre annonce!");
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ad);
            $em->flush();

            $this->addFlash("success", "Votre annonce a bien été supprimée!");
        }

        return $this->redirectToRoute("user_publications");
    }

    /**
     * @Route(
     *     "/annonces/detail/{id}",
     *     name="ad_detail",
     *     requirements={"id": "\d+"}
     * )
     */
    public function detail($id)
    {
        $adRepo = $this->getDoctrine()->getRepository(Ad::class);
        $ad = $adRepo->find($id);



        return $this->render("ad/detail.html.twig", ["ad" => $ad]);
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/annonces/depot/", name="ad_create")
     */
    public function adCreate(Request $request, Slugifier $slugifier, Mailer $mailer)
    {
        //crée une instance vide d'annonce
        $ad = new Ad();
        //crée un formulaire, et l'associe à notre instance
        $adForm = $this->createForm(AdType::class, $ad);

        //récupère les données envoyées, et les injecte dans l'instance $ad
        $adForm->handleRequest($request);

        //on teste le captcha si le form est soumis
        if($adForm->isSubmitted()) {
            //les données à envoyer à Google
            $post = [
                'secret' => '6Lc-iWMUAAAAAAuXj7u6h88kZDARVtLnEraBEZNz',
                'response' => $request->request->get('g-recaptcha-response'),
                'remoteip' => $request->getClientIp(),
            ];

            //on utilise Guzzle pour faire la requête http à google
            //s'installe avec composer require guzzlehttp/guzzle
            $client = new \GuzzleHttp\Client();
            //requête POST sans vérification SSL
            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => $post,
                'verify' => false
            ]);

            //la réponse de google
            $body = json_decode($response->getBody());
            //si le captcha est invalide...
            if ($body->success === false) {
                //on ajoute une erreur au form
                $adForm->addError(new FormError("oups captcha!"));
            }
        }

        //si le formulaire est soumis et valide
        if ($adForm->isSubmitted() && $adForm->isValid()){

            //récupère l'entity manager
            $em = $this->getDoctrine()->getManager();

            //hydrate les champs manquants
            $ad->setDateCreated(new \DateTime());

            //récupère le user actuellement connecté
            $user = $this->getUser();
            //donne cet user comme Créateur de l'annonce
            $ad->setCreator($user);

            $slugifier->setAdSlug($ad);

            //demande de sauvegarder l'annonce
            $em->persist($ad);
            //exécute la requête sql
            $em->flush();

            //message flash qui s'affichera sur la prochaine page
            $this->addFlash("success", "Votre annonce a été publiée !");

            //envoie un mail à l'admin (voir src/Mailer/Mailer.php)
            $mailer->sendNewAdWarning($ad);

            //redirige vers l'upload
            return $this->redirectToRoute("upload", ["id" => $ad->getId()]);
        }

        return $this->render('ad/create.html.twig', [
            "adForm" => $adForm->createView() //facile à oublier !
        ]);
    }
}












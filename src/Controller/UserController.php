<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{

    /**
     * @Route("/mes-publications/", name="user_publications")
     */
    public function publications()
    {
        //pas besoin d'aucune requête SQL !

        return $this->render("user/publications.html.twig");
    }


    /**
     * @Route("/inscription/", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //attention, ça doit être un tableau de rôles, y'a des crochets !
            $user->setRoles(["ROLE_USER"]);

            $user->setDateRegistered(new \DateTime());

            //hash le mot de passe
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Votre compte a bien été créé!");
            return $this->redirectToRoute("login");
        }

        return $this->render('user/register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion/", name="login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $lastError = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        return $this->render('user/login.html.twig', [
            "lastError" => $lastError,
            "lastUsername" => $lastUsername
        ]);
    }

    /**
     * @Route("/deconnexion/", name="logout")
     */
    //c'est Symfony qui s'occupe du logout !
    public function logout(){}
}

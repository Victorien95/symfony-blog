<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            if ($form->isValid()){
                // encryptage du mdp à partir de la config "encoders" de config/packages/security.yaml
                $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encodedPassword);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre compte est créé');

                return $this->redirectToRoute('app_index_index');

            }else{
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render('user/register.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/connexion")
     */
    public function login(AuthenticationUtils $utils)
    {
        // fait l'authentification et retourne une erreur
        $error = $utils->getLastAuthenticationError();
        // retourne l'identifiant saisi en cas de mauvaise authentification
        $lastUsername = $utils->getLastUsername();
        if(!empty ($error)){
            $this->addFlash('error', 'Identification incorrects');
        }

        return $this->render('user/login.html.twig',
            [
                'last_username' => $lastUsername
            ]
        );
    }

    /**
     * @Route("/deconnexion")
     */
    public function logout(){

    }


}

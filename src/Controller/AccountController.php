<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdade;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    // Ici L'objet autentication utils 
    // Offre des utilitaires qui conserne l'autentification
    public function login(AuthenticationUtils $utils) { // Bizarrerie du form_login 
      //Symfony va interceper les informations, on ne les traite pas nous-mêmes
      // symfony va intercepter le formulaire pour valider si l'utilisateur c'est utilisateur de sit ou pas
        
        // ici on voit la dernier erreur avec une dump($error) on rentre de fause info pour verifier si ca affiche les erreur
        $error = $utils->getLastAuthenticationError();

        //permet de recupere la dernier nom d'utilisateur qui a etais connecté
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username //permet de recupere la dernier nom d'utilisateur qui a etais connecté
        ]);
    }

    /**
     * Permet de se deconnecter
     * 
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout() {
        // .. rein !
    }
    
    /**
     * Permet d'afficher les formulaire d'insciption
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
       $user = new User();

       $form = $this->createForm(RegistrationType::class, $user);
        
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid ()) {
           // on demande de encode la entyte $user a qui je passe le mot de passe
           // ici le second $user->getHash()est = aus mot password se considere comment un mod de passe = Password 
           // donc encode moi ça et tu le met dans la (variable $hash)
           $hash = $encoder->encodePassword($user, $user->getHash());

           // je te remodifie ton hash avec ce que je vient de encoder
           $user->setHash($hash);

           $manager->persist($user);
           $manager->flush();

           $this->addFlash(
               'success',
               "Votre compte a bien éte créé ! <br/> Vous pouvez maintenant vous connecter !"
           );

           // redirection ver la page de connecxion apre avoir créé sont compte
           return $this->redirectToRoute('account_login');

       }
       return $this->render('account/registration.html.twig', [
           'form' =>$form->createView()
       ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * 
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager) {
        // Récupérer l'utilisateur connecté
        // Grâce à la function getUser() du controller !
        // pour obtenir l'utilisateur que et acctuelement connecté
       $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
       
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()) {
           // il n'est normalement pas nécessaire de persister une entité qui existe déjà
           $manager->persist($user);
           $manager->flush();

           $this->addFlash(
               'success', 
               "Les données du profil ont été enregistrée avec succés !"
           );
       }
       
        return $this->render('account/profile.html.twig', [
            // ici on demande a twig de nous créé la veu de formulaire
            'form' => $form->createView() 
        ]);
    }
    
    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update", name="account_password")
     *
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function updatePassword(
        Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager) {

        $passwordUpdate = new PasswordUpdade();
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
         
             // 1. Vérifier que le oldPassword du formulaire soit le mème que le password de l'user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
                // en metant une ! devant on dit si ici ce ne  pas vrai alors on a une probleme 
                // on doit Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));

            } else {
                // et si non se que se ok 
                // ici ont chope le nouveau mot de passe rentre apre modification 
                $newPassword = $passwordUpdate->getNewPassword();
                // j'ai besoin de encoder pour encode le mot de passe dans la entite user
                $hash = $encoder->encodePassword($user, $newPassword);
               
                // je veut te modifier ton hash et je  vais te remetre un nouveau
                 $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                // on ajoute un message flash pour dire que se ok 
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('homepage');
            }
            
            
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * 
     * @Route("/account", name="account_index")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount() {
        return $this->render('user/index.html.twig', [
            //RAPELE: Function getUser()
        // Dans le controller, getUser() nous donne l'utilisateur connecté
            'user' => $this->getUser()
        ]);
    }

    /**
     * Permet d'afficher la liste des réservations faites par l'utilisateur
     * 
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return Response
     */
    public function booking() {
        return $this->render('account/bookings.html.twig');
    }

}

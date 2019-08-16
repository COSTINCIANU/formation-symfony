<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils){
        // la class authenticationutils donne des outils sympas pour les erreurs d'authentification
         
        // Bizarrerie du form_login 
        //Symfony va interceper les informations, on ne les traite pas nous-mêmes
        // symfony va intercepter le formulaire pour valider si l'utilisateur c'est utilisateur de sit ou pas

        // ici on voit la dernier erreur avec une dump($error) on rentre de fause info pour verifier si ca affiche les erreur
        $error = $utils->getLastAuthenticationError();

        //permet de recupere la dernier adresse mail rentre
        $username = $utils->getLastUsername();
        
        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !== null, // ici difernet de null cella veut dire que se un booleen
            'username' => $username  //permet de recupere la dernier nom d'utilisateur qui a etais connecté
       
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/admin/logout", name="admin_account_logout")
     *
     * @return void
     */
    public function logout() {
        //
    }
}

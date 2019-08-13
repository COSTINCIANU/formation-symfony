<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdController extends AbstractController
{
    /**
     * Permet de afficer la liste des annonces
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo){  
        // function findAll() du repository 
        // Elle perment de récupérer tous les enregistrements de la table visée
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

     /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     * 
     * isgranted permet de s'assurer que l'utilisateur possède un certain rôle
     * Rappel: le ROLE_USER C'est le rôle que l'on donne par défaut à tous les utilisateurs connectés
     * 
     * @return Response
     */
    // ici on injecte la Request $request pour recupere les 
    // donne de formuaire apre sa soumition en POST de la page new.html.twig 
    public function create(Request $request, ObjectManager $manager){
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
         
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()){
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            // ici on fait le lien entre l'author et les annonce pour afficher les author des chaque annonce 
            // Rappel : on oblient le utilisateur connecté avec la function getUser()
            // Disont que l'AUTHOR de cette annonce ce l'utilisateur que c'est connnecté 
            $ad->setAuthor($this->getUser());
            
            $manager->persist($ad); // on demande de se prepare pour recevoir les information en bdd
              $manager->flush(); // on les enregistre en bdd  definitevement 

              $this->addFlash(
                // Flash pour prevenire le visiteur de enregistement 
                    'success',
                    "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !" 
                 );  

              return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()
              ]);     
            }

              return $this->render('ad/new.html.twig', ['form' => $form->createView()]
            );
    }

    /**
     * Perment de afficher le formulaire de edition avec de expression de securite
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", 
     * message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier") 
     * @return Response
     */

    // ici je veut enpeche l'utilisateur de edite un annonce si ne pas lui même l'Author
    // non seulement que la personne soit garanti utilisateur connecté avec 
    // isgranted role_user et que soit le même utilisateur que l'author de annonce 
    // Annotation security Permet plus de flexibilité que isgranted grâce aux "Expression"
    // Les expressions de sécurité Sorte de syntaxe logique exprimant des conditions d'accés
    public function edit(Ad $ad, Request $request, ObjectManager $manager){
        $form = $this->createForm(AdType::class, $ad);
         
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            
                $manager->persist($ad); // on demande de se prepare pour recevoir les information en bdd
                $manager->flush(); // on les enregistre en bdd  definitevement 

                $this->addFlash(
                    // Flash pour prevenire le visiteur de enregistement 
                        'success',
                        "Les modification de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !" 
                    );  

              return $this->redirectToRoute('ads_show',
               ['slug' => $ad->getSlug()
              ]);     
            }


        return $this->render('ad/edit.html.twig', [
            'form' =>  $form->createView(),
            'ad' => $ad

        ]);
    }
     
    /**
     * 
     * Permet d'afficher une seule annonce
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad) {
        // je recupere l'annonce qui corespond au slug ! 
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
            ]);
    } 


    
     /**
      * Permet de supprimer une annonce 
      *
      * @Route("/ads/{slug}/delete", name="ads_delete")
      *
      * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez pas le droit d'accéder à cette ressource")
      * rappele:secutity permet d'analyser plus finement la situation que isgranted
      *
      * @param Ad $ad
      * @param ObjectManager $manager
      * @return Response
      */
    public function delete(Ad $ad, ObjectManager $manager) {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien éte supprimée !"
        );
        
        return $this->redirectToRoute("ads_index");
    }


}



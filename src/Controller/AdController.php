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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * Permet de créer un formulair pour créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
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
     * Perment de afficher le formulaire de edition
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @return Response
     */
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


}



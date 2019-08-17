<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     * 
     * ou on peut mettre aussi un autre contrainte  option requirements exemple ça : , requirements={"page": "\d+"} ou  ça ("/admin/ads/{page<\d+>?1}", name="admin_ads_index" 
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {   

        $pagination->setEntityClass(Ad::class)
                   ->setPage($page);
                   // ->setRoute('admin_ads_index');
        
        //     $limit = 10;
        //     $start = $page * $limit - $limit;
        //     // exemple de calcul 
        //     // 1 * 10 = 10 - 10 = 0
        //     // 2 * 20 = 20 - 10 = 10  ect ... ect...
        // $total = count($repo->findAll());
        // $pages = ceil($total / $limit);  
        // // ceil function php pour arrondire le ciffre float exemple 3.4 => 4
        //     // Méthode find : permet de retrouver un enregistrement par son identifiant
        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
   
    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager) {
        // ici on enpeche la personne de supprime l'annonce si il ya déjà de réservation faite sur le même annonce
        // si le compte de réservation et supperieur à 0 alors fait un flash et je previent que se ne pas posible de supprime cette annonce
        if(count($ad->getBookings()) > 0) {
            $this->addFlash(
              'warning',
              "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong>
               car elle possède déjà des réservations !"
            );
        } else {
            // on demande au manager de supprime l'annonce
            $manager->remove($ad);
            // et on confireme la suppresion de l'annonce dans la bdd avec la manager flush()
            $manager->flush();
    
            // on fait une message flash 
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
            );
        }
        
        return $this->redirectToRoute('admin_ads_index');
    }
    
}

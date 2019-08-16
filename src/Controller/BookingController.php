<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        // ici on demande a handleRquest de regarde la requete que on le passe
        $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()) {
           // on chope l'utilisateur accuelement connecté
           $user = $this->getUser();

           $booking->setBooker($user)
                   ->setAd($ad);

            // Si les dates ne sont pas disponibles, message d'erreur
            if(!$booking->isBookableDates()) {
                // on demande afficher une message flash pour prevenire l'utilisateur si les date sont prise 
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisi ne peuvent être réservées : elles sont déjà prise."
                );
            } else {
                // Si non enregistrement et redirection 
               $manager->persist($booking);
               $manager->flush();
               
               // ici on donne en parametre le id pour pouvoir le appele par la suite 
               return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 
                    'withAlert' => true]);
            }
    }


        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     * 
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager) {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // alors le commentaire et relier a une annonce
            // et cette annonce sera celle de la réservation
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());  
                    // Rappele getUser nous renvoie au sain
                    // d'un controller de l'utilisateur qui est actuelement connecté
        
            $manager->persist($comment); // avec persist on demande de se prepare de recevoir les donnée
            $manager->flush();  // avec le flush() on demande de envoier réealemen la requete en bdd

            // envoier un message flash de success de information 
            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte !"
            );
          }


       return $this->render('booking/show.html.twig', [
           'booking' => $booking,
           'form'    => $form->createView()
       ]);
    }
}

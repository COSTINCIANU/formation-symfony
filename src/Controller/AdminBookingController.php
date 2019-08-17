<?php

namespace App\Controller;


use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination)
    {  
        // pagination des page de nous reservation ou comentaire ou autre 
        $pagination->setEntityClass(Booking::class)
                   ->setPage($page);
                  // ->setTemplatePath('admin/partials/pagination.html.twig');
        
        // $bookings = $pagination->getData();

        // // $limit = 10;
        // // $start = $page * $limit - $limit;
        // // exemple de calcul 
        // // 1 * 10 = 10 - 10 = 0
        // // 2 * 20 = 20 - 10 = 10  ect ... ect...
        // $total = count($repo->findAll());
        // $pages = ceil($total / 10);  


        return $this->render('admin/booking/index.html.twig', [
            'pagination' =>  $pagination
            
        ]);
    }

    /**
     * Permet d'éditer une réservation
     * 
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @return Response
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager) {
       $form = $this->createForm(AdminBookingType::class, $booking);

       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()) {
         $booking->setAmount(0);
            
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°{$booking->getId()} a bien été modifiée"
            );

        return $this->redirectToRoute("admin_booking_index");

       }
        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * 
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager) {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation a bien été supprimée"
        );

       return $this->redirectToRoute('admin_booking_index');
    }
}

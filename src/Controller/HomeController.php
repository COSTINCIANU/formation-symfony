<?php 

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {
   
  /**
     * Permet d'afficher  la route hompage
     *@Route("/", name="homepage")
     *
     * @return Response
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo) {
        return $this->render(
          'home.html.twig', [
            'ads' => $adRepo->findBestAds(3),
            'users' => $userRepo->findBestUsers(2)
          ]
        );
    }

}

?>



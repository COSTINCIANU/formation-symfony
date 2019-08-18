<?php

namespace App\Controller;


use App\Service\StatsService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $statsService)
    {   
        // REQUETE DQL 
        // SÉLECTION D'ENTITÉS:
        // En DQL on se interese pas aux tables, mais aux entités!
        $stats    = $statsService->getStats();
        $bestAds  = $statsService->getBestAds('DESC');
        $worstAds = $statsService->getWorstAds('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
        // function Compact() de php permet de créer un tableau automatiquement en nommant les clés
            'stats'    =>  $stats,
            'bestAds'  =>  $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}

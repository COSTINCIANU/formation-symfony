<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class StatsService {
    private $manager;

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
    }

    public function getStats() {
        $users    = $this->getUsersCount();
        $ads      = $this->getAdsCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();

        return compact('users', 'ads', 'bookings', 'comments');
        // function Compact() de php permet de créer un tableau automatiquement en nommant les clés
    }
    
    public function getUsersCount() {
          // REQUETE DQL 
        // SÉLECTION D'ENTITÉS:
        // En DQL on se interese pas aux tables, mais aux entités!
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        // Méthode GetSinglescalarreSult() perment d'obtenire le résultat sous la forme d'une variable scalaire simple
        // Méthode GetResult() Récupère les résultats sous forme d'objects Entité (ici des objects User)
    }

    public function getAdsCount() {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getBookingsCount() {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getCommentsCount() {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    
    // public function getAdsStats($direction) {
    //     return  $this->manager->createQuery(
    //         'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
    //         FROM App\Entity\Comment c
    //         JOIN c.ad a 
    //         JOIN a.author u
    //         GROUP BY a
    //         ORDER BY note' . $direction)->setMaxResults(5)->getResult();
    //         // JOIN c.ad a  commaintaire de l'annonce = c.rating
    //         // JOIN a.author u   utilisateur  de l'annonce
    //         // GROUP BY a   groupe par annonce 
    //         // ORDER BY note DESC ordone par note en ordre desandant


    // }
    public function getBestAds() {
        return  $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a 
            JOIN a.author u
            GROUP BY a
            ORDER BY note DESC')->setMaxResults(5)->getResult();
            // JOIN c.ad a  commaintaire de l'annonce = c.rating
            // JOIN a.author u   utilisateur  de l'annonce
            // GROUP BY a   groupe par annonce 
            // ORDER BY note DESC ordone par note en ordre desandant


    }
    public function getWorstAds() {
        return  $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a 
            JOIN a.author u
            GROUP BY a
            ORDER BY note ASC')->setMaxResults(5)->getResult();
            // JOIN c.ad a  commaintaire de l'annonce = c.rating
            // JOIN a.author u   utilisateur  de l'annonce
            // GROUP BY a   groupe par annonce 
            // ORDER BY note DESC ordone par note en ordre desandant


    }
  

}
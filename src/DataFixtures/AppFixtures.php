<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Booking;

class AppFixtures extends Fixture
{   
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {   
        // la librerie slagify pour génére de titre d'annonce alleatoire
        // $slugify = new Slugify();

        //la librerie FZANINOTTO/FAKER se pour crée de fauce donne mais realiste en français
        $faker = Factory::create('FR-fr');

        // grace a ce code on se créer un noveau Role que est le Role Admin
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // grace au code on crée un utilisateur qui aurra le Role Admin
        $adminUser = new User();
        $adminUser->setFirstName('Gina')
                  ->setLastName('Costincianu')
                  ->setEmail('gheorghina.costincianu@sfr.fr')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('http://www.avatars-gratuits.com/nature/avatar-24.jpg')
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>' .  join('</p></p>', $faker->paragraphs(3)) . '</p>')
                  ->addUserRole($adminRole); // la on dit je veut que tu ajoute a cette persone le add adminRole
        
        // je veut que le manager persist cette utilisateur la que se un utilisateur une peux particulier que se un AdminUser 
        $manager->persist($adminUser);

        // Nous gérons les utilisateurs
        $users = [];

        // declaration d'un tableau pour les homme et pour les femme
        $genres = ['male', 'female'];

        for($i = 1; $i <= 10; $i++) {
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            // condition ternaire si se un homme ou une femme pour nous avtar
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
            
            // on declare le encoder pour la Entité User  
            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' .  join('</p></p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

            // on persiste le user
            $manager->persist($user);
            // et a la fin de persiste on l'ajoute a un tableaux de Users 
            $users[] = $user;
        }


        // Nous génrons les annonces
        for($i = 1; $i <= 30; $i++){  
            // boucle for pour les annonces
            $ad = new Ad();

            $title       = $faker->sentence(); 
            $couverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content     = '<p>' .  join('</p></p>', $faker->paragraphs(5)) . '</p>';
          
            // ici on appele l'utilisateur que il y a dans le 
            // tableaux - 1 si un jour on change le nr de 10 
            // qui il a declare dans la boucle for des utilissateur
            $user = $users[mt_rand(0, count($users) - 1)];
            
            // ici on configure l'annonce
            $ad->setTitle($title)
               ->setCoverImage($couverImage)
               ->setIntroduction($introduction)
               ->setContent($content)
               ->setRooms(mt_rand(1, 5))
               ->setPrice(mt_rand(40, 200))
               ->setAuthor($user);

            for($j = 1; $j <= mt_rand(2, 5); $j++) {
                // boucle for pour afficher l'image
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                $manager->persist($image);  // persist l'image dabord
            } 

            // Gestion des réservations
            // for pour donner une nr alleatoit entre 0, 10 
            for($j = 1; $j <= mt_rand(0, 10); $j++) {
                $booking = new Booking();
                
                // on demande a faker une date au minimum 6 mois et maximum maitenat
                $createdAt = $faker->dateTimeBetween('-6 months');
                // la date quand je veut venir dans l'apartement quand la reservation a commencer 
                $startDate = $faker->dateTimeBetween('-3 months');

             // Gestion de la date fin 
                // la date de reservation entre 3 et 10 jours
                $duration = mt_rand(3, 10); 
                // la methode modify permet de ajoute ou enlever de jours a notre reservation pour le calcule exact de notre sejour
                // endDate se saurra ma startDate a la quel je vu ajouter duration :Exemple  donc si ça fait 6 on ajoute 6 jours 
                // ici pour pas modifier la vrais date on fait une clone de la date sur la methode elle meme
                $endDate  = (clone $startDate)->modify("+$duration days");
                // ici on conte le montant de la location
                // donc ad on veut le pris de l'annonce et on le multiplie par la duration de sejour
                // cette a dire le nombre de jours de location
               
               
                $amount = $ad->getPrice() * $duration;
                // le booker se un utilisateur aux assard qui vient reserve
                // ici on cherche un user dans le tableau des users 
                // aux assar par mis disponible et le -1 parceque se finit tjr a -1 veut que ça commance a zero
                $booker = $users[mt_rand(0, count($users) -1)];
                // declaration de la variable comment 
                $comment = $faker->paragraph();

            // Configuration de  notre booker
                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);

                $manager->persist($booking);
            }
    
            // persist() previent Doctrine qu'on veut sauver
            $manager->persist($ad); // persist l'annonce 
        }
        //flush() envoi la requête finale et pour que enregistre en une seul fois le 30 anonce dans la bdd
        $manager->flush();   // et en dernier on flush()pour enregistre dans la bdd
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Image;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {   
        // la librerie slagify pour génére de titre d'annonce alleatoire
        // $slugify = new Slugify();

        //la librerie FZANINOTTO/FAKER se pour crée de fauce donne mais realiste en français
        $faker = Factory::create('FR-fr');

        for($i = 1; $i <= 30; $i++){  
            // boucle for pour les annonces
            $ad = new Ad();

            $title       = $faker->sentence(); 
            $couverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content     = '<p>' .  join('</p></p>', $faker->paragraphs(5)) . '</p>';
          
    
        $ad->setTitle($title)
               ->setCoverImage($couverImage)
               ->setIntroduction($introduction)
               ->setContent($content)
               ->setRooms(mt_rand(1, 5))
               ->setPrice(mt_rand(40, 200));

            for($j = 1; $j <= mt_rand(2, 5); $j++) {
                // boucle for pour afficher l'image
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                $manager->persist($image);  // persist l'image dabord
            } 
    
            // persist() previent Doctrine qu'on veut sauver
            $manager->persist($ad); // persist l'annonce 
        }
        //flush() envoi la requête finale et pour que enregistre en une seul fois le 30 anonce dans la bdd
        $manager->flush();   // et en dernier on flush()pour enregistre dans la bdd
    }
}

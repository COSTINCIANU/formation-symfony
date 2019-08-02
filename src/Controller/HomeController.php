<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {

    /**
     * Montre la page qui dit Bonjour.....
     * 
     *@Route("/bonjour/{prenom}/age/{age}", name="hello")
     *@Route("/salut", name="hello_base")
     *@Route("/bonjour/{prenom}", name="hello_prenom")
     *
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0){
      return $this->render(
        'hello.html.twig', 
        [
          'prenom' => $prenom,
          'age' => $age
        ]
        );
    }
    /**
     * Permet d'afficher  la route hompage
     *@Route("/", name="homepage")
     * 
     */
    public function home() {
      $prenoms = ["Gina" => 25, "Petre" => 26, "Bianca" => 23, "Ion" => 42];

      return $this->render(
        'home.html.twig',
        ['title' => 'gfdhdghùytùjyt',
        'age' => 17,
        'tableau' => $prenoms]
      );
    }

}

?>



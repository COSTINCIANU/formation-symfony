<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request,
    $templatePath) {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');

        // SERVICE ET DÉPENDENCES 
       // Dans un service, l'injection se fait via le constructeur!  
        $this->manager = $manager;
        // ici on a fait l'injection via objectManager a la quel on a fait limport de class
        // grace a notre ObjectManager on peur recupere un Repository donc comment ça on fait appele a doctrine 
        $this->twig     = $twig;
        $this->templatePath = $templatePath;
    
    }

    public function setTemplatePath($templatePath) {
        $this->templatePath = $templatePath;
    }

    public function getTemplatePath() {
        return $this->templatePath;
    }

    public function setRoute($route) {
        $this->route = $route;
        
        return $this;
    }

    public function getRoute() {

        return $this->route;
    }

    public function display() {
        $this->twig->display($this->templatePath, [
            'page' =>  $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getPages() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
            Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Connaitre le total des enregistrements de la table grace au repository
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondire et le renvoyer
        $pages = ceil($total / $this->limit);

        // 3) renvoier la reponse
        return $pages;

    }

    public function getData() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
            Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Colculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
       
        // 2) Demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // 3) Renvoyer les éléments en questions
        return $data;
    }

    public function setPage($page) {
        $this->currentPage = $page;

        return $this;
    }

    public function getPage() {
        return$this->currentPage;
    }


    public function setlimit($limit) {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;

        return $this;
    }


    public function getEntityClass() {
        return $this->entityClass;
    }
}

<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date d'arrivée doit étre au bon format !")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être ultérieure à la date d'aujourd'hui !",
     * groups={"front"})
     */
    // Parametre Groups Permet de définire les groupes dans lesquels se place une validation
    // le simple fait de metre ça , groups={"front"} cella veut dire que on a pas validation de securyte et sur le front 
     
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date de départ doit étre au bon format !")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être plus éloignée que la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;
    
    /**
     * Callback appelé à chaque fois qu'on crée une réservation
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function prePersist() {
        // si ma date de creation elle est vide 
        if(empty($this->created)) {
        // et bien j'ai en vie que ca devient la nouvelle DateTime
           $this->createdAt = new \DateTime();
        }

        // maintenat on calcule le montant 
        // si le montant est vide 
        if(empty($this->amount)) {
            // alors on doit calculet 
            // prix de l'annonce * nombre de jour 
            // on stocke tout dans $this->amount que on a declare ici
            $this->amount = $this->ad->getPrice() *  $this->getDuration();
        }

    }


    public function isBookableDates() {
        // ici avec la function function isBookableDates()  on demande si ces datte sont posibles pour réserver
       // 1) Il faut  connaitre les dates qui sont impossible pour l'annonce
       $notAvailableDays = $this->ad->getNotAvailableDays(); // ici on chope les journe pour la quel cette annonce ne serra pas disponible
       // 2) Il faut comparer les dates choisies avec les dates imposibles 
       $bookingDays = $this->getDays(); // ici se l'ensemble de journées de ma réservation sur format de tableau DateTime


       $formatDay = function($day){
        return $day->format('Y-m-d');
       };

       // Tableau des chaînes de caractères de mes journées
       // je transforme mes objets dateTime de ma réservation en chaînes de caractères Y-m-d
       $days = array_map($formatDay, $bookingDays);

       // ici je fait la meme chose mais pour les journées qui sont pas 
       // disponible ce qui fait que j'ai deux tableu de chaînes de catactères de Datetime
       $notAvailable = array_map($formatDay, $notAvailableDays);

       // si on retrouve pas une de nous journe disponible ici 
       // donc je vais boucle sur chaque journées qui me conserne moi ma réservation
        // es que elle est presénte par mis les journées qui ne sont pas disponible
       foreach($days as $day) {
           // si se le cas ma function dit tout ça ne pas posible 
           if(array_search($day, $notAvailable) !== false) return false;
       }
       // si non la on return true pour me dire que tout a belle et bien functioner
       return true;
    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     *
     * @return array Un tableau d'objets DateTime représentent les jours de la réservation
     */
    public function getDays() {
         $resultat = range(
             $this->startDate->getTimestamp(),
             $this->endDate->getTimestamp(),
             24 * 60 * 60 
         );

        // la on transforme chaque une de date reçu dans le range onn le tansforme dans une Timestamp ici avec la function map
        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat); // ici on precise le nom de tableu que on veut modifier ici ce (resultat)

        return $days;
    }

    public function getDuration() {
        // on declarre une variable diff que fait referance a la
        // differance entre endDate et startDate qui est considere comment un interval entre les deux date
        // méthode diff des objets DATATIME
        // Fait la diférance entre 2 dates et renvoie un objet DateInterval
        $diff = $this->endDate->diff($this->startDate);

        // ici on demande le retur de differance entre les jours
        // on regarde le nombre des jours qui sont eculler entre la date de arrive et celle de depart
        return $diff->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}

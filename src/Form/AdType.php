<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{   /**
    * Perment d'avoir la configuration de base d'un champ ! 
    *
    * @param string $label
    * @param string $placeholder
    * @param array $options
    * @return array
    */
    // private ça veut dire que moi seul a acces a cette fonction de configuration 
    private function getConfiguration($label, $placeholder, $options = []) {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }


    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add(
            'title',
                TextType::class, $this->getConfiguration("Titre", "Tapez un super titre pour votre annonce"))
            ->add(
            'slug', 
                TextType::class, $this->getConfiguration("Adresse web", "tapez l'adresse web (automatique)", [
                    'required' => false
                ]))
            ->add(
            'coverImage', 
                UrlType::class, $this->getConfiguration("URL de l'image principale", "Donnez l'addresse d'une image qui donne vraiment envie"))
            ->add(
            'introduction', 
                TextType::class, $this->getConfiguration("Introduction", "Donnez une description globale de l'annonce"))
            ->add(
            'content', 
                TextareaType::class, $this->getConfiguration("Description détaillée", "Tapez une description qui donne vraiment en vie de nvenire chez vous !"))
            ->add(
            'rooms', 
                IntegerType::class, $this->getConfiguration("Nombre de chambres", "Le nombre de chambres disponibles"))
            ->add(
            'price', 
                MoneyType::class, $this->getConfiguration("Prix par nuit", "Indiquez le prix que vous voulez pour une nuit"));
    $builder->add(
            'images',
                 CollectionType::class, 
                    [
                        'entry_type' => ImageType::class,
                        // allow_add mis à true permet de dire oui se possible de ajouter au temps d'images que on veut
                        'allow_add' => true, 
                        // allow_delete permettre de supprimer des éléments liés
                        'allow_delete' => true
                        ]);
    } 


    public function configureOptions(OptionsResolver $resolver)
    {     $resolver->setDefaults
        ([
            'data_class' => Ad::class,
        ]);
    }
}

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType {
    // REFRACTORISATION se utiliser le code pour pas duplique le code
    
    // cette function fait appele a deux formulaire RegistrationType et AdType
    // si un jour je voudrais change la configuration les changement se repercute
    // sur le deux formulaire et sur tôt je modifie que dans une seur endroit cet a dire Ici
    /**
    * Perment d'avoir la configuration de base d'un champ ! 
    *
    * @param string $label
    * @param string $placeholder
    * @param array $options
    * @return array
    */
    // protected ça veut dire que je peut faire appele a cette fonction de configuration  
    // alors si elle est en protected les class que utilise ApplicationType pouron l'utiliser cette function
    protected function getConfiguration($label, $placeholder, $options = []) {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }

}
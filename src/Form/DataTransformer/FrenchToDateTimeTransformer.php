<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

// toute les transformer doit implementer de DataTransformInterface
class FrenchToDateTimeTransformer implements DataTransformerInterface {
     
    public function transform($date) {
        // avec le transform on envoie les donner au formulaire 
        // si date est egale a null on pura pas faire appele a la function format
        if($date === null){
            // alors elle returne rien 
            return '';
        }
        // ici elle returne une date transformer
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate) {
        // avec reverseTransform se quand on reçoit de donner de formulaire que le tansforme 
        // francgDate = 13/08/2019
        // es que franchDate est null
        if($frenchDate === null){
            // Exception
            throw new TransformationFailedException("Vous devez fournire une date !");
        }
        // on demande de crée une date su forma d/m/Y et de cette objet je veut sont formt DateTime
        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if($date === false){
            // Exception 
            throw new TransformationFailedException("Le format de la date n'est pas le bon !");
        }

        return $date;
    }
}



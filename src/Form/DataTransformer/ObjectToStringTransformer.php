<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectToStringTransformer implements DataTransformerInterface {

    public function transform($date) 
    {

    }

    // Je transforme la date du formulaire (au format texte) au format DateTime pour l'entité
    public function reverseTransform($role) 
    {

        return $role->getTitle();
    }
} 
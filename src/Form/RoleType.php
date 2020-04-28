<?php

namespace App\Form;

use App\Entity\Role;
use App\Form\DataTransformer\ObjectToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RoleType extends AbstractType
{
    private $transformer;

    public function __construct(ObjectToStringTransformer $transformer) {
        $this->transformer = $transformer;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', EntityType::class, [
                'class' => Role::class,
                'choice_label' => function($role) {
                    return $role->getTitle();
                }
            ])
        ;

        $builder->get('title')->addModelTransformer($this->transformer);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}

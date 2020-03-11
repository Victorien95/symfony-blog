<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                // nom du champ de formulaire
                'name',
                // type du champ (input text)
                TextType::class,
                // tableau d'options
                [
                    // contenu du label
                    'label' => 'Nom',
                    // attributs html de la balise en question ici input
                    'attr' =>
                        [
                            'placeholder' => 'Votre nom',
                            'class' => 'bg-light'
                        ],
                    'label_attr' =>
                        [
                            // attributs du lbael
                        ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}

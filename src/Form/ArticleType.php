<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',
                TextType::class,
                [
                    'label'=>'Titre',
                    'attr'=>
                        [
                            'placeholder'=> 'Titre'
                        ]
                ])
            ->add('content',
                TextareaType::class,
                [
                    'label' => 'Contenu',
                    'attr' =>
                        [
                            'palceholder' => 'Votre contenue'
                        ]
                ])
            ->add('category',
                EntityType::class,
                [
                    'label' => 'Catégories',
                    // nom de l'entité permet de faire un champ select avec pour valeurs les attributs de l'entité Catégorie
                    'class' => Category::class,
                    // attribut qu s'affiche dans le select
                    'choice_label' => 'name',
                    // pour avoir une 1ère option vide
                    'placeholder' => 'Choisissez une catégorie'
                ])
            ->add('image',
                FileType::class,
                [
                    'label' => 'Illustration',
                    // champ optionnel
                    'required' => false
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

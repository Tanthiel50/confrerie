<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('date')
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
            ])
            ->add('img', FileType::class, [
                'label' => 'Images',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'data_class' => null,
            ])
            ->add('slug')
            ;
            // ...

            $builder
                ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName', 
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}

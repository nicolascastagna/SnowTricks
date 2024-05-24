<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
            ])
            ->add('mainImage', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 5 Mo.'
                    ]),
                ],
            ])
            ->add('pictures', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'attr' => [
                        'placeholder' => 'Exemple : https://www.youtube.com/watch?v=LyfFuv4_wjQ&ab',
                        'title' => 'URL YouTube ou Dailymotion'
                    ],
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

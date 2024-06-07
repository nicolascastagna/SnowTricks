<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Écrivez un commentaire...', 'class' => 'textarea-comment'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s.,!?àèéêëîïôùûüç«»()°%+=;\'"\-_]+$/u',
                        'message' => 'Votre commentaire ne peut contenir que des lettres (avec accents), des chiffres, des guillemets et des caractères de ponctuation de base.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}

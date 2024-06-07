<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Exemple : https://www.youtube.com/watch?v=LyfFuv4_wjQ&ab',
                    'title' => 'URL YouTube ou Dailymotion'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '#^((?:https?:)?\/\/)?(?:www\.)?(youtube\.com|dai\.ly|dailymotion\.com)(\/(?:watch\?v=|embed\/|video\/|embed\/video\/)?)([\w\-]+)(\S+)?$#',
                        'message' => 'Les URL vidéos doivent être de Youtube ou Dailymotion.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}

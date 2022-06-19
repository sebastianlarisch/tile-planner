<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TilePlannerType extends AbstractType
{
    public const TYPE_OFFSET = 'offset';
    public const TYPE_CHESS = 'chess';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'tile_type',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'Art der Paneele',
                    'choices' => [
                        'Klickvinyl / Klicklaminat' => 'click',
                        'Fliese' => 'tile',
                    ]
                ]
            )
            ->add(
                'laying_type',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'Verlegemuster',
                    'choices' => [
                        'mit Versatz' => self::TYPE_OFFSET,
                        'Schachbrett' => self::TYPE_CHESS,
                    ]
                ]
            )
            ->add(
                'gap_width',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Fugenbreite [cm]',
                    'attr' => [
                        'placeholder' => '0,5'
                    ]
                ]
            )
            ->add(
                'room_width',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Raumbreite [cm]',
                    'attr' => [
                        'placeholder' => '450'
                    ]
                ]
            )
            ->add(
                'room_depth',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Raumtiefe [cm]',
                    'attr' => [
                        'placeholder' => '330'
                    ]
                ]
            )
            ->add(
                'tile_length',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Länge Paneele [cm]',
                    'attr' => [
                        'placeholder' => '120'
                    ]
                ]
            )
            ->add(
                'tile_width',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Breite Paneele [cm]',
                    'attr' => [
                        'placeholder' => '20'
                    ]
                ]
            )
            ->add(
                'min_tile_length',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Mindesbreite Paneele [cm]',
                    'attr' => [
                        'placeholder' => '30'
                    ]
                ]
            )
            ->add(
                'costs_per_square',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Kosten pro Quadratmeter [e.g. 12.99]',
                    'attr' => [
                        'placeholder' => '29,95'
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Erstellen',
                    'attr' => [
                        'class' => 'col-4 btn btn-primary',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

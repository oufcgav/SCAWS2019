<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PredictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Goalkeeper' => 'Goalkeeper',
                    'Defenders' => 'Defenders',
                    'Midfielders' => 'Midfielders',
                    'Strikers' => 'Strikers',
                ],
            ])
            ->add('time', ChoiceType::class,  [
                'choices' => [
                    'First half' => 'First half',
                    'Second half' => 'Second half',
                    '1-15 mins' => '1-15 mins',
                    '16-30 mins' => '16-30 mins',
                    '31-45 mins' => '31-45 mins',
                    '46-60 mins' => '46-60 mins',
                    '61-75 mins' => '61-75 mins',
                    '76-90 mins' => '76-90 mins',
                    'Stoppage time' => 'Stoppage time',
                ],
            ])
            ->add('atMatch', ChoiceType::class, [
                'label' => 'Are you going to the match? ',
                'choices' => [
                    'yes' => 'yes',
                    'no' => 'no',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('nice_time', ChoiceType::class, [
                'label' => 'Do you agree? ',
                'choices' => [
                    'yes' => 'yes',
                    'no' => 'no',
                    'I deny everything' => 'I deny everything',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('add', SubmitType::class)
        ;

    }
}

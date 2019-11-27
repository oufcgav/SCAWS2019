<?php

namespace App\Form\Type;

use App\Entity\Competition;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('opponent', TextType::class, ['label' => 'Opposition : '])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'data' => new \DateTimeImmutable(),
            ])
            ->add('location', ChoiceType::class, [
                'choices' => [
                    'Home' => 'Home',
                    'Away' => 'Away',
                ],
            ])
            ->add('competition', ChoiceType::class,  [
                'choices' => [
                   'League' => 'League',
                   'Other' => 'Other',
                ],
            ])
            ->add('add', SubmitType::class)
        ;
    }
}
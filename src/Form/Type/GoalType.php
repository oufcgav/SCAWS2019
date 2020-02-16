<?php

namespace App\Form\Type;

use App\Repository\SquadList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class GoalType extends AbstractType
{
    /**
     * @var SquadList
     */
    private $squadList;

    public function __construct(SquadList $squadList)
    {
        $this->squadList = $squadList;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $squad = $this->squadList->getCurrentSquad();
        $scorers = array_combine($squad, $squad);

        $builder
            ->add('scorer', ChoiceType::class, [
                'choices' => $scorers
            ])
            ->add('timing', ChoiceType::class,  [
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
            ->add('add', SubmitType::class)
        ;
    }
}

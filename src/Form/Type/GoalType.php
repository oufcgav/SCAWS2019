<?php

namespace App\Form\Type;

use App\Entity\GoalTimes;
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
        $scorers = array_merge(['Select player' => ''], array_combine($squad, $squad));
        $times = array_merge(['Select time' => ''], array_combine(array_values(GoalTimes::toArray()), array_values(GoalTimes::toArray())));
        $times['Extra time/other'] = 'Extra time/other';
        unset($times[GoalTimes::FIRST_HALF()->getValue()]);
        unset($times[GoalTimes::SECOND_HALF()->getValue()]);

        $builder
            ->add('scorer', ChoiceType::class, [
                'choices' => $scorers,
            ])
            ->add('timing', ChoiceType::class, [
                'choices' => $times,
            ])
            ->add('add', SubmitType::class)
        ;
    }
}

<?php

namespace App\Form\Type;

use App\Repository\PredictionRepository;
use App\Security\User;
use App\Service\PredictionRestriction;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;

class PredictionType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var PredictionRepository
     */
    private $predictionRestriction;

    public function __construct(
        Security $security,
        PredictionRestriction $predictionRestriction
    ) {
        $this->security = $security;
        $this->predictionRestriction = $predictionRestriction;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        if (!$user || !$user instanceof User) {
            throw new RuntimeException('Not logged in');
        }
        $availablePositions = $this->predictionRestriction->getPositions($user);
        $availableTimes = $this->predictionRestriction->getTimings($user);

        $builder
            ->add('position', ChoiceType::class, [
                'choices' => $availablePositions,
            ])
            ->add('time', ChoiceType::class, [
                'choices' => $availableTimes,
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

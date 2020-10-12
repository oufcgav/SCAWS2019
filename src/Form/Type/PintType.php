<?php

namespace App\Form\Type;

use App\Security\UserProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PintType extends AbstractType
{
    /**
     * @var UserProvider
     */
    private $userList;

    public function __construct(UserProvider $userList)
    {
        $this->userList = $userList;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', ChoiceType::class, [
                'choices' => $this->userList->listUsernames(),
            ])
            ->add('add', SubmitType::class)
        ;
    }
}

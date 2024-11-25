<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'Nom',
            'required' => true,
        ]);
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmez le mot de passe'],
        ]);
        $builder->add('email', TextType::class, [
            'label' => 'Email',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'attr' => [
                'rows' => 5,
                'cols' => 50,
            ],
        ]);
        $builder->add('roles', ChoiceType::class, [
            'choices' => array_flip([
                'ROLE_ADMIN' => 'Administrateur',
                'ROLE_USER' => 'Invité',
            ]),
            'label' => 'Rôles',
            'multiple' => true,

        ]);
        $builder->add('access', CheckboxType::class, [
            'label' => 'Accès',
            'data' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

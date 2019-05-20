<?php

namespace App\Form;

use App\Form\DTO\UserData;
use App\Validator\UniqueUsername;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegistrationFormType
 * @package App\Form
 */
class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new UniqueUsername(['message' => 'The value "{{ value }}" already exists.'])
                ]
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('alpha')
                ]
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('alpha')
                ]
            ])
            ->add('birthday', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'format' => 'yyyy/MM/dd'
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserData::class
        ]);
    }
}

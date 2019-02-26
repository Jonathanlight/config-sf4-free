<?php

namespace App\Form\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('genre', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'genre.man'=> Utilisateur::GENRE_MAN,
                    'genre.woman' => Utilisateur::GENRE_WOMAN,
                ]
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => 'register.nom',
                ]
            ])
            ->add('prenom', TextType::class, [
                    'attr' => [
                        'placeholder' => 'register.prenom',
                    ]
                ])
            ->add('datenaissance', DateType::class, [
                'label' => 'register.birthday',
                'placeholder' => 'register.birthday',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'mt-5',
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'form.user.email',
                ]
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'placeholder' => 'register.phone',
                ]
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'form.user.messagePassword',
                'first_options'  => array('label' => 'register.password', 'attr' => ['placeholder' => 'form.user.password']),
                'second_options' => array('label' => 'register.passwordConfirm', 'attr' => ['placeholder' => 'register.passwordConfirm']),
            ))
            ->add('submit', SubmitType::class, [
                'label' => 'register.inscription',
                'attr' => [
                    'class' => 'btn waves-effect waves-light col s12 gradient-45deg-cryptizy gradient-shadow',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

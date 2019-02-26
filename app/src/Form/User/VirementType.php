<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class VirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montant_depot', MoneyType::class, [
                'data' => 0
            ])
            ->add('frais', MoneyType::class, [
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('montant', MoneyType::class, [
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'virement.submit',
                'attr' => [
                    'class' => 'waves-effect waves-light btn gradient-45deg-amber-amber gradient-shadow',
                ]
            ])
        ;
    }
}

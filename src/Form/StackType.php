<?php

namespace App\Form;

use App\Entity\Stack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('category', ChoiceType::class, [
                'label' => 'category',
                'choices' => [
                    'Languages' => 'Languages',
                    'Frameworks' => 'Frameworks',
                    'Tools' => 'Tools',
                    'OS' => 'OS',
                ],

            ])
            ->add('stack', FileType::class, [
                'label' => 'Stack Logo',
                'required' => true,
                'multiple' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stack::class,
        ]);
    }
}

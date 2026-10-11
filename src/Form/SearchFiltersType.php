<?php

namespace App\Form;

use App\DTO\SearchFiltersDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lat')
            ->add('lng')
            ->add('area')
            ->add('bortle')
            ->add('radius')
            ->add('elevation')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchFiltersDTO::class,
        ]);
    }
}

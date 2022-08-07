<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\ShinobiConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShinobiConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('schema', ChoiceType::class, [
                'choices' => ['http' => 'http', 'https' => 'https'],
                'label' => 'Schema',
            ])
            ->add('host', TextType::class, [
                'label' => 'Host',
            ])
            ->add('port', NumberType::class, [
                'html5' => true,
                'scale' => 0,
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'Api key',
            ])
            ->add('groupKey', TextType::class, [
                'label' => 'Group key',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ShinobiConfig::class,
            ]
        );
    }
}
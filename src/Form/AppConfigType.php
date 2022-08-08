<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\AppConfig;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $timezones = DateTimeZone::listIdentifiers();

        $builder
            ->add('timezone', ChoiceType::class, [
                'label' => 'Time zone',
                'choices' => array_combine(
                    str_replace('/', ' / ', $timezones),
                    $timezones,
                ),
            ])
            ->add('shinobi', ShinobiConfigType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => AppConfig::class,
            ]
        );
    }
}
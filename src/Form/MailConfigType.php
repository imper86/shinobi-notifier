<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\NotificationSenderConfig\MailConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MailConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, ['label' => 'Enabled', 'required' => false])
            ->add('dsn', TextType::class, ['label' => 'DSN'])
            ->add('from', EmailType::class, ['label' => 'From'])
            ->add('to', EmailType::class, ['label' => 'To'])
            ->add('subject', TextType::class, ['label' => 'Subject'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => MailConfig::class,
                'empty_data' => new MailConfig(
                    true,
                    'smtp://user:pass@smtp.example.com:port',
                    'change@me.plz',
                    'change@me.plz',
                    'New video found by Shinobi notifier',
                ),
            ],
        );
    }
}
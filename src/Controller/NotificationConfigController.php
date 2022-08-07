<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AppConfigRepository;
use App\Service\NotificationSender\NotificationSenderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class NotificationConfigController extends AbstractController
{
    public function __construct(
        private readonly ServiceLocator $senderLocator,
        private readonly AppConfigRepository $configRepository
    ) {
    }

    #[Route('/notifications', 'app.notification_config.index')]
    public function list(): Response
    {
        $senderConfigs = $this->configRepository->get()->getNotificationSenders() ?? [];

        return $this->render('notification_config/list.html.twig', ['configs' => $senderConfigs]);
    }

    #[Route('/notifications/create', 'app.notification_config.create_select_type')]
    public function createSelectType(): Response
    {
        /** @var NotificationSenderInterface[] $senders */
        $senders = array_map(
            fn(string $id) => $this->senderLocator->get($id),
            array_keys($this->senderLocator->getProvidedServices()),
        );

        return $this->render('notification_config/create_select_type.html.twig', ['senders' => $senders]);
    }

    #[Route('/notifications/create/{serviceId}', 'app.notification_config.create')]
    public function create(string $serviceId, Request $request): Response
    {
        /** @var NotificationSenderInterface $sender */
        $sender = $this->senderLocator->get($serviceId);
        $form = $this->createForm($sender::getConfigType());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config = $this->configRepository->get();
            $config->addNotificationSender($form->getData());

            $this->configRepository->save($config);

            return $this->redirectToRoute('app.notification_config.index');
        }

        return $this->render(
            'notification_config/create.html.twig',
            ['sender' => $sender, 'form' => $form->createView()]
        );
    }

    #[Route('/notifications/edit/{key}', 'app.notification_config.edit')]
    public function edit(int $key, Request $request): Response
    {
        $config = $this->configRepository->get();
        $senderConfig = $config->getNotificationSender($key);

        if (null === $senderConfig) {
            $this->addFlash('danger', sprintf('Notification config with key %d does not exist', $key));

            return $this->redirectToRoute('app.notification_config.index');
        }

        /** @var NotificationSenderInterface $senderService */
        $senderService = $this->senderLocator->get($senderConfig::getServiceId());
        $form = $this->createForm($senderService::getConfigType(), $senderConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->changeNotificationSender($key, $senderConfig);

            $this->configRepository->save($config);

            return $this->redirectToRoute('app.notification_config.index');
        }

        return $this->render(
            'notification_config/edit.html.twig',
            [
                'key' => $key,
                'senderConfig' => $senderConfig,
                'senderService' => $senderService,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route('/notifications/delete/{key}', 'app.notification_config.delete')]
    public function delete(int $key): Response
    {
        $config = $this->configRepository->get();
        $config->removeNotificationSender($key);

        $this->configRepository->save($config);

        return $this->redirectToRoute('app.notification_config.index');
    }
}
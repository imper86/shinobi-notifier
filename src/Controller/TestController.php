<?php

namespace App\Controller;

use App\Service\AppConfigRepository;
use App\Service\NotificationSender\SlackNotificationSender;
use App\Service\ShinobiApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function __construct(
        private readonly SlackNotificationSender $slackNotificationSender,
        private readonly AppConfigRepository $configRepository,
        private readonly ShinobiApi $shinobiApi,
    ) {
    }

    #[Route('/tests', 'app.test')]
    public function test(): Response
    {
//        $config = $this->configRepository->get();
//
//        $this->slackNotificationSender->send($config->getNotificationSender(0), 'Test!');

        $videos = $this->shinobiApi->getVideos();

        return new JsonResponse($videos);
    }
}
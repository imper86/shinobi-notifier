<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AppConfigRepository;
use App\Service\ShinobiApi;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MainController extends AbstractController
{
    public function __construct(
        private readonly ShinobiApi $shinobiApi,
        private readonly AppConfigRepository $configRepository
    ) {
    }

    #[Route('/', 'app.main')]
    public function index(): Response
    {
        try {
            $monitors = $this->shinobiApi->getMonitors();

            return $this->render(
                'main/index.html.twig',
                [
                    'monitors' => $monitors,
                ]
            );
        } catch (ClientExceptionInterface $exception) {
            $this->addFlash('danger', sprintf('Shinobi connection failed: %s', $exception->getMessage()));

            return $this->redirectToRoute('app.config.edit');
        }
    }

    #[Route("/toggle/{monitorId}", "app.main.toggle_monitor")]
    public function toggleMonitor(string $monitorId): Response
    {
        $config = $this->configRepository->get();

        if (in_array($monitorId, $config->activeMonitorIds, true)) {
            array_splice(
                $config->activeMonitorIds,
                array_search($monitorId, $config->activeMonitorIds),
                1
            );
        } else {
            $config->activeMonitorIds[] = $monitorId;
        }

        $this->configRepository->save($config);

        return $this->redirectToRoute('app.main');
    }
}
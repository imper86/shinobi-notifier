<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\MonitorToggleService;
use App\Service\ShinobiApi;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MainController extends AbstractController
{
    public function __construct(
        private readonly ShinobiApi $shinobiApi,
        private readonly MonitorToggleService $monitorToggleService,
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
        $this->monitorToggleService->toggle($monitorId);

        return $this->redirectToRoute('app.main');
    }

    #[Route("/monitors/activate", "app.main.monitors.activate")]
    public function activateMonitors(): Response
    {
        $this->monitorToggleService->turnOnAll();

        return $this->redirectToRoute('app.main');
    }

    #[Route("/monitors/deactivate", "app.main.monitors.deactivate")]
    public function deactivateMonitors(): Response
    {
        $this->monitorToggleService->turnOffAll();

        return $this->redirectToRoute('app.main');
    }
}
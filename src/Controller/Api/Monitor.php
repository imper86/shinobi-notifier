<?php

namespace App\Controller\Api;

use App\Service\MonitorToggleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Monitor extends AbstractController
{
    public function __construct(private readonly MonitorToggleService $monitorToggleService)
    {
    }

    #[Route('/api/monitor/{monitorId}', 'app.api.monitor.put')]
    public function put(Request $request, string $monitorId): Response
    {
        $body = json_decode($request->getContent(), true);

        if (isset($body['status'])) {
            if (true === $body['status']) {
                $this->monitorToggleService->turnOn($monitorId);
            } else {
                $this->monitorToggleService->turnOff($monitorId);
            }
        }

        return new Response();
    }
}
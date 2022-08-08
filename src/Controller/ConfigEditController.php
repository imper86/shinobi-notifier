<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NoConfigException;
use App\Form\AppConfigType;
use App\Service\AppConfigRepository;
use App\Service\ShinobiApi;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfigEditController extends AbstractController
{
    public function __construct(
        private readonly AppConfigRepository $configRepository,
        private readonly ShinobiApi $shinobiApi
    ) {
    }

    #[Route("/config", "app.config.edit")]
    public function edit(Request $request): Response
    {
        try {
            $config = $this->configRepository->get(false);
        } catch (NoConfigException) {
            $config = $this->configRepository->createEmpty();
        }

        $form = $this->createForm(AppConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->isEmpty = false;
            $this->configRepository->save($config);

            try {
                $this->shinobiApi->testConnection();
                $this->addFlash('success', 'Config saved. Connection ok.');
            } catch (ClientExceptionInterface $exception) {
                $this->addFlash(
                    'danger',
                    sprintf('Config saved, but connection failed: %s', $exception->getMessage())
                );
            }
        }

        return $this->render('config_edit/edit.html.twig', ['form' => $form->createView()]);
    }
}
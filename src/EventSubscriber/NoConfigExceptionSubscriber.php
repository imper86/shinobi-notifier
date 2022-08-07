<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\NoConfigException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

final class NoConfigExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NoConfigException) {
            $session = $this->requestStack->getSession();

            if ($session instanceof Session) {
                $session->getFlashBag()->add('warning', 'No config found. Please fill form below and submit');
            }

            $event->setResponse(new RedirectResponse($this->router->generate('app.config.edit')));
        }
    }
}
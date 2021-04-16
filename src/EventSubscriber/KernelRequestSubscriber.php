<?php

namespace Karkov\Kcms\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelRequestSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::REQUEST => [
                ['processRequest', 10],
                ['logRequest', 0],
            ],
        ];
    }

    public function processRequest(RequestEvent $event)
    {
        $this->logger->info('process from subscriber');
    }

    public function logRequest(RequestEvent $event)
    {
        $this->logger->info('log from subscriber');
    }
}

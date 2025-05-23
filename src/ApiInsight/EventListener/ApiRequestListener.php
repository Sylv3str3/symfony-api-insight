<?php

namespace ApiInsight\EventListener;

use ApiInsight\Service\MetricsStorageInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiRequestListener
{
    private array $startTimes = [];
    
    public function __construct(
        private MetricsStorageInterface $metricsStorage,
        private bool $enabled = true
    ) {
    }
    
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->enabled || !$event->isMainRequest()) {
            return;
        }
        
        $request = $event->getRequest();
        $requestId = spl_object_hash($request);
        
        // Enregistrer le temps de début
        $this->startTimes[$requestId] = microtime(true);
    }
    
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->enabled || !$event->isMainRequest()) {
            return;
        }
        
        $request = $event->getRequest();
        $requestId = spl_object_hash($request);
        
        if (!isset($this->startTimes[$requestId])) {
            return;
        }
        
        $route = $request->attributes->get('_route') ?? 'unknown';
        $statusCode = $event->getResponse()->getStatusCode();
        $duration = microtime(true) - $this->startTimes[$requestId];
        
        // Ne pas enregistrer les routes de métriques elles-mêmes
        if ($route === 'api_insight_metrics') {
            return;
        }
        
        $this->metricsStorage->recordApiCall($route, $statusCode, $duration);
        unset($this->startTimes[$requestId]);
    }
    
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->enabled || !$event->isMainRequest()) {
            return;
        }
        
        $request = $event->getRequest();
        $requestId = spl_object_hash($request);
        
        if (!isset($this->startTimes[$requestId])) {
            return;
        }
        
        $route = $request->attributes->get('_route') ?? 'unknown';
        $statusCode = 500; // Par défaut pour les exceptions
        $duration = microtime(true) - $this->startTimes[$requestId];
        
        $this->metricsStorage->recordApiCall($route, $statusCode, $duration);
        unset($this->startTimes[$requestId]);
    }
}
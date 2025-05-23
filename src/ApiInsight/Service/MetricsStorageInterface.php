<?php

namespace ApiInsight\Service;

interface MetricsStorageInterface
{
    /**
     * Enregistre une requête API
     */
    public function recordApiCall(string $route, int $statusCode, float $duration): void;
    
    /**
     * Récupère toutes les métriques
     */
    public function getMetrics(): array;
    
    /**
     * Récupère les métriques pour une route spécifique
     */
    public function getRouteMetrics(string $route): array;
    
    /**
     * Réinitialise toutes les métriques
     */
    public function reset(): void;
} 
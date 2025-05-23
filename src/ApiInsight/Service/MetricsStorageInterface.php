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

    /**
     * Récupère les métriques pour une période spécifique
     * 
     * @param string $period La période ('minute', 'hour', 'day', 'month', 'year')
     * @return array Les métriques pour la période
     */
    public function getMetricsForPeriod(string $period = 'day'): array;
    
    /**
     * Récupère les métriques pour une route spécifique et une période donnée
     * 
     * @param string $route La route à analyser
     * @param string $period La période ('minute', 'hour', 'day', 'month', 'year')
     * @return array Les métriques pour la route et la période
     */
    public function getRouteMetricsForPeriod(string $route, string $period = 'day'): array;
} 
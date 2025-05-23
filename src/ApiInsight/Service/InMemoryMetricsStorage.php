<?php

namespace ApiInsight\Service;

class InMemoryMetricsStorage implements MetricsStorageInterface
{
    private array $metrics = [];
    
    public function recordApiCall(string $route, int $statusCode, float $duration): void
    {
        if (!isset($this->metrics[$route])) {
            $this->metrics[$route] = [
                'total_calls' => 0,
                'status_codes' => [],
                'total_duration' => 0,
                'min_duration' => PHP_FLOAT_MAX,
                'max_duration' => 0,
                'errors' => 0,
            ];
        }
        
        $this->metrics[$route]['total_calls']++;
        $this->metrics[$route]['total_duration'] += $duration;
        
        if ($duration < $this->metrics[$route]['min_duration']) {
            $this->metrics[$route]['min_duration'] = $duration;
        }
        
        if ($duration > $this->metrics[$route]['max_duration']) {
            $this->metrics[$route]['max_duration'] = $duration;
        }
        
        if (!isset($this->metrics[$route]['status_codes'][$statusCode])) {
            $this->metrics[$route]['status_codes'][$statusCode] = 0;
        }
        
        $this->metrics[$route]['status_codes'][$statusCode]++;
        
        // Compter les erreurs (4xx et 5xx)
        if ($statusCode >= 400) {
            $this->metrics[$route]['errors']++;
        }
    }
    
    public function getMetrics(): array
    {
        $result = [];
        
        foreach ($this->metrics as $route => $metrics) {
            $result[$route] = $this->calculateRouteMetrics($metrics);
        }
        
        return $result;
    }
    
    public function getRouteMetrics(string $route): array
    {
        if (!isset($this->metrics[$route])) {
            return [];
        }
        
        return $this->calculateRouteMetrics($this->metrics[$route]);
    }
    
    public function reset(): void
    {
        $this->metrics = [];
    }
    
    private function calculateRouteMetrics(array $metrics): array
    {
        $avgDuration = $metrics['total_calls'] > 0 
            ? $metrics['total_duration'] / $metrics['total_calls'] 
            : 0;
        
        return [
            'total_calls' => $metrics['total_calls'],
            'avg_duration' => round($avgDuration, 3),
            'min_duration' => $metrics['min_duration'] === PHP_FLOAT_MAX ? 0 : round($metrics['min_duration'], 3),
            'max_duration' => round($metrics['max_duration'], 3),
            'status_codes' => $metrics['status_codes'],
            'errors' => $metrics['errors'],
            'error_rate' => $metrics['total_calls'] > 0 
                ? round(($metrics['errors'] / $metrics['total_calls']) * 100, 2) 
                : 0,
        ];
    }
} 
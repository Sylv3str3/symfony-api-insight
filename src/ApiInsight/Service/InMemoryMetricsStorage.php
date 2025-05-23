<?php

namespace ApiInsight\Service;

class InMemoryMetricsStorage implements MetricsStorageInterface
{
    private array $metrics = [];
    private array $timeMetrics = [];
    
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
        
        // Enregistrer les métriques temporelles
        $now = new \DateTime();
        $date = $now->format('Y-m-d');
        $hour = $now->format('H');
        $minute = $now->format('i');
        $month = $now->format('Y-m');
        $year = $now->format('Y');
        
        // Métriques journalières
        if (!isset($this->timeMetrics['daily'][$date][$route])) {
            $this->timeMetrics['daily'][$date][$route] = [
                'total_calls' => 0,
                'errors' => 0,
                'total_duration' => 0,
            ];
        }
        $this->timeMetrics['daily'][$date][$route]['total_calls']++;
        $this->timeMetrics['daily'][$date][$route]['total_duration'] += $duration;
        if ($statusCode >= 400) {
            $this->timeMetrics['daily'][$date][$route]['errors']++;
        }
        
        // Métriques horaires
        if (!isset($this->timeMetrics['hourly'][$date][$hour][$route])) {
            $this->timeMetrics['hourly'][$date][$hour][$route] = [
                'total_calls' => 0,
                'errors' => 0,
                'total_duration' => 0,
            ];
        }
        $this->timeMetrics['hourly'][$date][$hour][$route]['total_calls']++;
        $this->timeMetrics['hourly'][$date][$hour][$route]['total_duration'] += $duration;
        if ($statusCode >= 400) {
            $this->timeMetrics['hourly'][$date][$hour][$route]['errors']++;
        }
        
        // Métriques par minute (pour les 60 dernières minutes)
        if (!isset($this->timeMetrics['minutely'][$date][$hour][$minute][$route])) {
            $this->timeMetrics['minutely'][$date][$hour][$minute][$route] = [
                'total_calls' => 0,
                'errors' => 0,
                'total_duration' => 0,
            ];
        }
        $this->timeMetrics['minutely'][$date][$hour][$minute][$route]['total_calls']++;
        $this->timeMetrics['minutely'][$date][$hour][$minute][$route]['total_duration'] += $duration;
        if ($statusCode >= 400) {
            $this->timeMetrics['minutely'][$date][$hour][$minute][$route]['errors']++;
        }
        
        // Métriques mensuelles
        if (!isset($this->timeMetrics['monthly'][$month][$route])) {
            $this->timeMetrics['monthly'][$month][$route] = [
                'total_calls' => 0,
                'errors' => 0,
                'total_duration' => 0,
            ];
        }
        $this->timeMetrics['monthly'][$month][$route]['total_calls']++;
        $this->timeMetrics['monthly'][$month][$route]['total_duration'] += $duration;
        if ($statusCode >= 400) {
            $this->timeMetrics['monthly'][$month][$route]['errors']++;
        }
        
        // Métriques annuelles
        if (!isset($this->timeMetrics['yearly'][$year][$route])) {
            $this->timeMetrics['yearly'][$year][$route] = [
                'total_calls' => 0,
                'errors' => 0,
                'total_duration' => 0,
            ];
        }
        $this->timeMetrics['yearly'][$year][$route]['total_calls']++;
        $this->timeMetrics['yearly'][$year][$route]['total_duration'] += $duration;
        if ($statusCode >= 400) {
            $this->timeMetrics['yearly'][$year][$route]['errors']++;
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
        $this->timeMetrics = [];
    }
    
    public function getMetricsForPeriod(string $period = 'day'): array
    {
        switch ($period) {
            case 'minute':
                return $this->getMinutelyMetrics();
            case 'hour':
                return $this->getHourlyMetrics();
            case 'month':
                return $this->getMonthlyMetrics();
            case 'year':
                return $this->getYearlyMetrics();
            case 'day':
            default:
                return $this->getDailyMetrics();
        }
    }
    
    public function getRouteMetricsForPeriod(string $route, string $period = 'day'): array
    {
        $allMetrics = $this->getMetricsForPeriod($period);
        $routeMetrics = [];
        
        foreach ($allMetrics as $timeKey => $metrics) {
            if (isset($metrics[$route])) {
                $routeMetrics[$timeKey] = $metrics[$route];
            }
        }
        
        return $routeMetrics;
    }
    
    private function getDailyMetrics(): array
    {
        if (!isset($this->timeMetrics['daily'])) {
            return [];
        }
        
        $result = [];
        foreach ($this->timeMetrics['daily'] as $date => $routeMetrics) {
            $result[$date] = [];
            foreach ($routeMetrics as $route => $metrics) {
                $avgDuration = $metrics['total_calls'] > 0 
                    ? round($metrics['total_duration'] / $metrics['total_calls'], 3) 
                    : 0;
                
                $result[$date][$route] = [
                    'total_calls' => $metrics['total_calls'],
                    'errors' => $metrics['errors'],
                    'avg_duration' => $avgDuration,
                    'error_rate' => $metrics['total_calls'] > 0 
                        ? round(($metrics['errors'] / $metrics['total_calls']) * 100, 2) 
                        : 0,
                ];
            }
        }
        
        return $result;
    }
    
    private function getHourlyMetrics(): array
    {
        if (!isset($this->timeMetrics['hourly'])) {
            return [];
        }
        
        $result = [];
        foreach ($this->timeMetrics['hourly'] as $date => $hourlyData) {
            foreach ($hourlyData as $hour => $routeMetrics) {
                $timeKey = "$date $hour:00";
                $result[$timeKey] = [];
                
                foreach ($routeMetrics as $route => $metrics) {
                    $avgDuration = $metrics['total_calls'] > 0 
                        ? round($metrics['total_duration'] / $metrics['total_calls'], 3) 
                        : 0;
                    
                    $result[$timeKey][$route] = [
                        'total_calls' => $metrics['total_calls'],
                        'errors' => $metrics['errors'],
                        'avg_duration' => $avgDuration,
                        'error_rate' => $metrics['total_calls'] > 0 
                            ? round(($metrics['errors'] / $metrics['total_calls']) * 100, 2) 
                            : 0,
                    ];
                }
            }
        }
        
        return $result;
    }
    
    private function getMinutelyMetrics(): array
    {
        if (!isset($this->timeMetrics['minutely'])) {
            return [];
        }
        
        $result = [];
        foreach ($this->timeMetrics['minutely'] as $date => $hourlyData) {
            foreach ($hourlyData as $hour => $minutelyData) {
                foreach ($minutelyData as $minute => $routeMetrics) {
                    $timeKey = "$date $hour:$minute";
                    $result[$timeKey] = [];
                    
                    foreach ($routeMetrics as $route => $metrics) {
                        $avgDuration = $metrics['total_calls'] > 0 
                            ? round($metrics['total_duration'] / $metrics['total_calls'], 3) 
                            : 0;
                        
                        $result[$timeKey][$route] = [
                            'total_calls' => $metrics['total_calls'],
                            'errors' => $metrics['errors'],
                            'avg_duration' => $avgDuration,
                            'error_rate' => $metrics['total_calls'] > 0 
                                ? round(($metrics['errors'] / $metrics['total_calls']) * 100, 2) 
                                : 0,
                        ];
                    }
                }
            }
        }
        
        return $result;
    }
    
    private function getMonthlyMetrics(): array
    {
        if (!isset($this->timeMetrics['monthly'])) {
            return [];
        }
        
        $result = [];
        foreach ($this->timeMetrics['monthly'] as $month => $routeMetrics) {
            $result[$month] = [];
            foreach ($routeMetrics as $route => $metrics) {
                $avgDuration = $metrics['total_calls'] > 0 
                    ? round($metrics['total_duration'] / $metrics['total_calls'], 3) 
                    : 0;
                
                $result[$month][$route] = [
                    'total_calls' => $metrics['total_calls'],
                    'errors' => $metrics['errors'],
                    'avg_duration' => $avgDuration,
                    'error_rate' => $metrics['total_calls'] > 0 
                        ? round(($metrics['errors'] / $metrics['total_calls']) * 100, 2) 
                        : 0,
                ];
            }
        }
        
        return $result;
    }
    
    private function getYearlyMetrics(): array
    {
        if (!isset($this->timeMetrics['yearly'])) {
            return [];
        }
        
        $result = [];
        foreach ($this->timeMetrics['yearly'] as $year => $routeMetrics) {
            $result[$year] = [];
            foreach ($routeMetrics as $route => $metrics) {
                $avgDuration = $metrics['total_calls'] > 0 
                    ? round($metrics['total_duration'] / $metrics['total_calls'], 3) 
                    : 0;
                
                $result[$year][$route] = [
                    'total_calls' => $metrics['total_calls'],
                    'errors' => $metrics['errors'],
                    'avg_duration' => $avgDuration,
                    'error_rate' => $metrics['total_calls'] > 0 
                        ? round(($metrics['errors'] / $metrics['total_calls']) * 100, 2) 
                        : 0,
                ];
            }
        }
        
        return $result;
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
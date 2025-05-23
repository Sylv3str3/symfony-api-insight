<?php

namespace ApiInsight\Controller;

use ApiInsight\Service\MetricsStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends AbstractController
{
    public function __construct(
        private MetricsStorageInterface $metricsStorage,
        private bool $authEnabled = false,
        private string $authType = 'token',
        private ?string $authToken = null,
        private bool $prometheusEnabled = false
    ) {
    }

    /**
     * @Route("/metrics", name="api_insight_metrics", methods={"GET"})
     */
    public function getMetrics(Request $request): Response
    {
        // Vérification de l'authentification si activée
        if ($this->authEnabled) {
            if (!$this->checkAuth($request)) {
                return new JsonResponse(['error' => 'Accès non autorisé'], Response::HTTP_UNAUTHORIZED);
            }
        }

        $format = $request->query->get('format', 'json');
        $metrics = $this->metricsStorage->getMetrics();
        
        // Calcul des métriques globales
        $totalCalls = 0;
        $totalErrors = 0;
        $routes = 0;
        
        foreach ($metrics as $routeMetrics) {
            $totalCalls += $routeMetrics['total_calls'];
            $totalErrors += $routeMetrics['errors'];
            $routes++;
        }
        
        $globalMetrics = [
            'total_calls' => $totalCalls,
            'total_errors' => $totalErrors,
            'error_rate' => $totalCalls > 0 ? round(($totalErrors / $totalCalls) * 100, 2) : 0,
            'routes_count' => $routes,
        ];
        
        // Format Prometheus si demandé et disponible
        if ($format === 'prometheus' && $this->prometheusEnabled) {
            return $this->getPrometheusResponse($metrics, $globalMetrics);
        }
        
        // Format JSON par défaut
        return new JsonResponse([
            'global' => $globalMetrics,
            'routes' => $metrics,
        ]);
    }
    
    /**
     * @Route("/metrics/reset", name="api_insight_metrics_reset", methods={"POST"})
     */
    public function resetMetrics(Request $request): Response
    {
        // Vérification de l'authentification si activée
        if ($this->authEnabled) {
            if (!$this->checkAuth($request)) {
                return new JsonResponse(['error' => 'Accès non autorisé'], Response::HTTP_UNAUTHORIZED);
            }
        }
        
        $this->metricsStorage->reset();
        
        return new JsonResponse(['status' => 'success', 'message' => 'Métriques réinitialisées']);
    }
    
    private function checkAuth(Request $request): bool
    {
        if ($this->authType === 'token') {
            $token = $request->headers->get('X-API-Insight-Token');
            return $token === $this->authToken;
        } elseif ($this->authType === 'jwt') {
            // JWT non disponible dans la version gratuite
            return false;
        }
        
        return false;
    }
    
    private function getPrometheusResponse(array $metrics, array $globalMetrics): Response
    {
        $output = "# HELP api_insight_total_calls Nombre total d'appels API\n";
        $output .= "# TYPE api_insight_total_calls counter\n";
        $output .= "api_insight_total_calls {instance=\"global\"} {$globalMetrics['total_calls']}\n";
        
        $output .= "# HELP api_insight_errors Nombre total d'erreurs API\n";
        $output .= "# TYPE api_insight_errors counter\n";
        $output .= "api_insight_errors {instance=\"global\"} {$globalMetrics['total_errors']}\n";
        
        $output .= "# HELP api_insight_error_rate Taux d'erreur API en pourcentage\n";
        $output .= "# TYPE api_insight_error_rate gauge\n";
        $output .= "api_insight_error_rate {instance=\"global\"} {$globalMetrics['error_rate']}\n";
        
        foreach ($metrics as $route => $routeMetrics) {
            $routeName = str_replace(['"', '\\', "\n", "\r"], ['\"', '\\\\', '', ''], $route);
            
            $output .= "api_insight_total_calls {route=\"{$routeName}\"} {$routeMetrics['total_calls']}\n";
            $output .= "api_insight_errors {route=\"{$routeName}\"} {$routeMetrics['errors']}\n";
            $output .= "api_insight_error_rate {route=\"{$routeName}\"} {$routeMetrics['error_rate']}\n";
            $output .= "api_insight_avg_duration {route=\"{$routeName}\"} {$routeMetrics['avg_duration']}\n";
        }
        
        return new Response($output, 200, ['Content-Type' => 'text/plain']);
    }
} 
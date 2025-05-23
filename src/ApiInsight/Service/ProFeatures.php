<?php

namespace ApiInsight\Service;

/**
 * Cette classe contient des méthodes pour vérifier si les fonctionnalités Pro sont disponibles.
 * Dans la version gratuite, ces méthodes retournent toujours false.
 */
class ProFeatures
{
    /**
     * Vérifie si l'export Prometheus est disponible
     */
    public static function isPrometheusExportAvailable(): bool
    {
        return false;
    }
    
    /**
     * Vérifie si l'authentification JWT est disponible
     */
    public static function isJwtAuthAvailable(): bool
    {
        return false;
    }
    
    /**
     * Vérifie si le dashboard web est disponible
     */
    public static function isDashboardAvailable(): bool
    {
        return false;
    }
    
    /**
     * Vérifie si le stockage Redis est disponible
     */
    public static function isRedisStorageAvailable(): bool
    {
        return false;
    }
    
    /**
     * Vérifie si le stockage en base de données est disponible
     */
    public static function isDatabaseStorageAvailable(): bool
    {
        return false;
    }
    
    /**
     * Vérifie si les alertes sont disponibles
     */
    public static function areAlertsAvailable(): bool
    {
        return false;
    }
    
    /**
     * Vérifie si la limitation de débit est disponible
     */
    public static function isRateLimitingAvailable(): bool
    {
        return false;
    }
} 
<?php

namespace ApiInsight\Service;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class MetricsStorageFactory
{
    public static function create(string $storageType): MetricsStorageInterface
    {
        return match ($storageType) {
            'memory' => new InMemoryMetricsStorage(),
            'redis' => throw new ServiceNotFoundException('Redis storage est disponible uniquement dans la version Pro'),
            'database' => throw new ServiceNotFoundException('Database storage est disponible uniquement dans la version Pro'),
            default => throw new \InvalidArgumentException(sprintf('Type de stockage "%s" non supporté', $storageType)),
        };
    }
} 
# API Insight - Métriques & Monitoring pour Symfony APIs

![Version](https://img.shields.io/badge/version-1.1.0-blue.svg)
![PHP](https://img.shields.io/badge/php-8.2%2B-purple.svg)
![Symfony](https://img.shields.io/badge/symfony-6.x%20%7C%207.x-black.svg)



API Insight est un bundle Symfony ultra-léger qui ajoute des métriques, de la visibilité et un point d'observation (`/metrics`) à toutes tes routes API
## 🚀 Installation

```bash
composer require api-insight/metrics-bundle
```

## ⚙️ Configuration

Ajouter le bundle dans `config/bundles.php`:

```php
return [
    // ...
    ApiInsight\ApiInsightBundle::class => ['all' => true],
];
```

Créer le fichier de configuration `config/packages/api_insight.yaml`:

```yaml
api_insight:
    enabled: true
    storage: memory # Options: memory, redis, database (Pro)
    auth:
        enabled: false
        type: token # Options: token, jwt (Pro)
        token: null # Votre token secret
    prometheus:
        enabled: false # Disponible en version Pro
    dashboard:
        enabled: false # Disponible en version Pro
```

Importer les routes dans `config/routes.yaml`:

```yaml
api_insight:
    resource: '@ApiInsightBundle/Resources/config/routes.yaml'
```

## 📊 Utilisation

Une fois installé, API Insight commence automatiquement à collecter des métriques sur toutes les requêtes API.

### Accès aux métriques globales

Accédez à vos métriques via l'endpoint `/metrics`:

```bash
curl http://votre-api.com/metrics
```

Exemple de réponse:

```json
{
    "global": {
        "total_calls": 325,
        "total_errors": 12,
        "error_rate": 3.69,
        "routes_count": 8
    },
    "routes": {
        "api_users_get": {
            "total_calls": 120,
            "avg_duration": 0.056,
            "min_duration": 0.012,
            "max_duration": 0.234,
            "status_codes": {
                "200": 118,
                "404": 2
            },
            "errors": 2,
            "error_rate": 1.67
        },
        // ... autres routes
    }
}
```

### Accès aux métriques temporelles

Vous pouvez également accéder aux métriques réparties dans le temps via l'endpoint `/metrics/time`:

```bash
# Métriques journalières (par défaut)
curl http://votre-api.com/metrics/time

# Métriques period= {minute, hour, day, month, year}
curl http://votre-api.com/metrics/time?period=hour



# Métriques pour une route spécifique
curl http://votre-api.com/metrics/time?route=api_users_get
```

Exemple de réponse pour les métriques temporelles:

```json
{
    "period": "day",
    "metrics": {
        "2023-11-15": {
            "api_users_get": {
                "total_calls": 45,
                "errors": 2,
                "avg_duration": 0.067,
                "error_rate": 4.44
            },
            "api_products_list": {
                "total_calls": 38,
                "errors": 0,
                "avg_duration": 0.123,
                "error_rate": 0
            }
        },
        "2023-11-16": {
            // ... métriques pour ce jour
        }
    }
}
```

Exemple de réponse pour les métriques mensuelles:

```json
{
    "period": "month",
    "metrics": {
        "2023-11": {
            "api_users_get": {
                "total_calls": 1245,
                "errors": 23,
                "avg_duration": 0.062,
                "error_rate": 1.85
            },
            // ... autres routes
        },
        "2023-12": {
            // ... métriques pour ce mois
        }
    }
}
```

### Réinitialisation des métriques

```bash
curl -X POST http://votre-api.com/metrics/reset
```

## 🔒 Sécurité

Pour protéger l'accès à vos métriques, activez l'authentification:

```yaml
api_insight:
    auth:
        enabled: true
        type: token
        token: "votre-token-secret"
```

Puis accédez aux métriques avec l'en-tête d'authentification:

```bash
curl http://votre-api.com/metrics -H "X-API-Insight-Token: votre-token-secret"
```

## 🌟 Fonctionnalités

| Fonction | Version gratuite | Version Pro |
|----------|-----------------|------------|
| Compteur de requêtes API (par route) | ✅ | ✅ |
| Suivi des erreurs HTTP (4xx/5xx) | ✅ | ✅ |
| Latence moyenne par endpoint | ✅ | ✅ |
| Route /metrics en JSON | ✅ | ✅ |
| Métriques temporelles (minute/heure/jour/mois/année) | ✅ | ✅ |
| Export Prometheus | ❌ | ✅ |
| Export vers Grafana/Influx/Elastic | ❌ | ✅ |
| Authentification JWT ou Token | ❌ | ✅ |
| Dashboard web intégré | ❌ | ✅ |
| Limitation de débit & alertes | ❌ | ✅ |

## 📝 Licence

MIT

## 🤝 Support

Pour toute question ou assistance, [ouvrez une issue](https://github.com/api-insight/metrics-bundle/issues). 
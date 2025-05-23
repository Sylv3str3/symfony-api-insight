# Guide d'installation d'API Insight

Ce guide vous aidera à installer et configurer API Insight dans votre projet Symfony.

## Prérequis

- PHP 8.2 ou supérieur
- Symfony 6.x ou 7.x
- Composer

## Installation

### 1. Installer le package via Composer

```bash
composer require api-insight/metrics-bundle
```

### 2. Enregistrer le bundle

Ajouter le bundle dans `config/bundles.php`:

```php
return [
    // ... autres bundles
    ApiInsight\ApiInsightBundle::class => ['all' => true],
];
```

### 3. Configurer le bundle

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

### 4. Importer les routes

Ajouter les routes dans `config/routes.yaml`:

```yaml
api_insight:
    resource: '@ApiInsightBundle/Resources/config/routes.yaml'
```

Ou si vous préférez, dans `config/routes/api_insight.yaml`:

```yaml
api_insight:
    resource: '@ApiInsightBundle/Resources/config/routes.yaml'
```

### 5. Vérifier l'installation

Une fois l'installation terminée, vous pouvez accéder à l'endpoint `/metrics` pour vérifier que tout fonctionne correctement:

```bash
curl http://votre-api.com/metrics
```

Si vous voyez une réponse JSON avec des métriques (qui peuvent être vides au début), l'installation est réussie!

## Configuration avancée

### Sécuriser l'endpoint de métriques

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

### Désactiver temporairement la collecte de métriques

Si vous souhaitez désactiver temporairement la collecte de métriques:

```yaml
api_insight:
    enabled: false
```

## Résolution des problèmes

### Les métriques ne s'affichent pas

1. Vérifiez que le bundle est correctement enregistré dans `config/bundles.php`
2. Vérifiez que les routes sont correctement importées
3. Assurez-vous que `enabled` est défini sur `true` dans la configuration

### Erreur d'authentification

Si vous recevez une erreur 401 Unauthorized:

1. Vérifiez que vous avez correctement configuré le token d'authentification
2. Assurez-vous d'envoyer le bon en-tête `X-API-Insight-Token` avec la valeur exacte du token

### Problèmes de performance

La version gratuite utilise le stockage en mémoire, qui est réinitialisé à chaque redémarrage du serveur. Si vous avez besoin de persistance ou de meilleures performances, envisagez de passer à la version Pro qui offre des options de stockage Redis et base de données.
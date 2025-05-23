# Guide de mise à niveau vers la version 1.1.0

Ce guide vous aide à mettre à niveau API Insight de la version 1.0.0 vers la version 1.1.0.

## Étapes de mise à niveau

### 1. Mettre à jour le package via Composer

```bash
composer require api-insight/metrics-bundle:^1.1.0
```

### 2. Vider le cache (optionnel mais recommandé)

```bash
# Si vous utilisez Symfony CLI
symfony console cache:clear

# Ou avec la commande PHP
php bin/console cache:clear
```

## Nouvelles fonctionnalités

### Métriques temporelles avancées

La version 1.1.0 ajoute un support complet pour les métriques temporelles :

- Métriques par minute
- Métriques par heure 
- Métriques par jour
- Métriques par mois
- Métriques par année

### Utilisation des nouvelles fonctionnalités

Vous pouvez accéder aux métriques temporelles via le nouvel endpoint `/metrics/time` :

```bash
# Métriques par défaut (jour)
curl http://votre-api.com/metrics/time

# Définir une période spécifique
curl http://votre-api.com/metrics/time?period=hour
curl http://votre-api.com/metrics/time?period=minute
curl http://votre-api.com/metrics/time?period=day
curl http://votre-api.com/metrics/time?period=month
curl http://votre-api.com/metrics/time?period=year

# Filtrer par route
curl http://votre-api.com/metrics/time?route=api_users_get
```

## Compatibilité descendante

Cette mise à jour est entièrement compatible avec la version précédente. Aucune modification n'est nécessaire dans votre code existant ou dans votre configuration.

## Problèmes connus

Aucun problème connu à ce jour. Si vous rencontrez des difficultés avec cette mise à jour, veuillez [ouvrir une issue](https://github.com/api-insight/metrics-bundle/issues). 
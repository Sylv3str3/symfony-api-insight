# Changelog

Tous les changements notables de ce projet seront documentés dans ce fichier.

## [1.1.0] - 2025-05-23

### Ajouté
- Support des métriques temporelles avancées :
  - Métriques par minute
  - Métriques par heure
  - Métriques par jour
  - Métriques par mois
  - Métriques par année
- Endpoint `/metrics/time` avec paramètre `period` pour filtrer par période
- Support du filtrage par route spécifique via le paramètre `route`

### Modifié
- Amélioration de la documentation
- Optimisation du stockage en mémoire

## [1.0.0] - 2025-05-23

### Ajouté
- Première version stable
- Collecte automatique des métriques API
- Suivi des codes de statut
- Calcul de la latence moyenne, minimale et maximale
- Calcul du taux d'erreur
- Endpoint `/metrics` pour accéder aux métriques
- Support du stockage en mémoire
- Authentification par token (optionnelle) 
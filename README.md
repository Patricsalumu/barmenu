# Le Comptoir — menu numérique

Application Laravel pour créer et gérer le menu public d’un bar ou restaurant. Les données sont stockées dans SQLite ; le menu est consultable sans compte et le propriétaire accède à la gestion avec un code.

## Démarrage

Prérequis : PHP 8.2+, Composer et l’extension PHP SQLite.

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Ouvrir `http://127.0.0.1:8000`. Le seeder crée l’établissement de démonstration **Le Comptoir**, son menu et le code propriétaire initial **1234**. Modifiez ce code depuis l’espace propriétaire après la première connexion.

## Fonctionnalités

- Menu public avec recherche par nom/description et filtres par catégorie ou sous-catégorie.
- Gestion du nom, type d’activité, slogan, adresse, numéro WhatsApp, logo, couleur du thème, devise (`$` ou `F`) et taux de conversion.
- Gestion des catégories principales, sous-catégories et produits (nom, description, prix, visibilité).
- Les images de logo sont stockées sur le disque public Laravel.
- Les prix du menu sont exprimés dans la devise sélectionnée. Le taux est exprimé en F pour 1 $.

Pour recharger les données de démonstration sans supprimer les données existantes :

```bash
php artisan db:seed
```

Pour repartir de zéro en développement :

```bash
php artisan migrate:fresh --seed
```

L’application est conçue pour une entreprise configurée par base de données. Pour une installation publique, remplacez le code de démonstration et configurez HTTPS et une sauvegarde du fichier SQLite.

## Tests

```bash
php artisan test
```

# MondayPrayer (Koinonia)

Application Laravel de suivi de jeûne et de prière pour une equipe (participants, dirigeants, statistiques, rappels et administration).

## Stack technique

- PHP 8.2+
- Laravel 12
- Node.js 20+ (Vite + Tailwind)
- Base de donnees: SQLite (par defaut) ou PostgreSQL
- Queue Laravel (notifications)

## Fonctionnalites principales

- Authentification utilisateur
- Tableau de bord personnel
- Gestion des jeunes (`fasts`)
- Gestion des dirigeants / personnes (`leaders`)
- Sessions de priere (creation, pause, reprise, compteur, cloture)
- Statistiques utilisateur
- Centre de notifications
- Preferences de jeune par profil
- Espace administration (roles, rappels manuels, statistiques)

## Installation rapide (local)

### Prerequis

- PHP 8.2+
- Composer
- Node.js + npm
- Une base SQLite ou PostgreSQL

### Option 1: installation en une commande

```bash
composer run setup
```

Ce script execute automatiquement:

1. `composer install`
2. creation de `.env` depuis `.env.example` (si absent)
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

### Option 2: installation manuelle

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

## Demarrage en developpement

Commande recommandee (tout-en-un):

```bash
composer run dev
```

Cette commande lance en parallele:

- serveur Laravel
- worker de queue (`queue:listen`)
- logs Laravel (`pail`)
- Vite en mode dev

Alternative manuelle:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

## Base de donnees

Par defaut, `.env.example` est configure en SQLite.

Pour SQLite:

1. conserver `DB_CONNECTION=sqlite`
2. creer le fichier `database/database.sqlite` si necessaire
3. lancer `php artisan migrate`

Pour PostgreSQL, adapter les variables `DB_*` dans `.env`.

## Taches planifiees et rappels

Une commande metier envoie des rappels de jeune:

- `koinonia:fasting-reminders`

Planification actuelle (dans `routes/console.php`):

- execution quotidienne a `20:30`

En local, pour simuler le scheduler:

```bash
php artisan schedule:work
```

## Commandes metier utiles

### Rappels

```bash
php artisan koinonia:fasting-reminders
```

### Import CSV detaille

```bash
php artisan koinonia:import-fasts "chemin/vers/fichier.csv" --dry-run
php artisan koinonia:import-fasts "chemin/vers/fichier.csv"
```

Options disponibles:

- `--user-id=`
- `--delimiter=,`
- `--dry-run`

### Import CSV de synthese mensuelle

```bash
php artisan koinonia:import-summary "chemin/vers/synthese.csv" 2026 --dry-run
php artisan koinonia:import-summary "chemin/vers/synthese.csv" 2026
```

## Tests et qualite

Lancer les tests:

```bash
composer run test
# ou
php artisan test
```

Formatage (Pint):

```bash
./vendor/bin/pint
```

## Build front

Build production:

```bash
npm run build
```

Dev server front:

```bash
npm run dev
```

## Deploiement

Le projet contient:

- `Dockerfile`
- `render.yaml`

Le demarrage en environnement cible applique les migrations puis lance Laravel sur le port `10000`.

## Structure rapide

- `app/Http/Controllers`: logique applicative
- `app/Models`: modeles metier (jeunes, priere, personnes, etc.)
- `app/Console/Commands`: commandes artisan metier
- `routes/web.php`: routes web
- `routes/console.php`: planification scheduler
- `resources/views`: vues Blade

## Notes

- L'application utilise des notifications en base et par email (si email utilisateur defini).
- Les routes d'administration sont protegees par un middleware de role admin.
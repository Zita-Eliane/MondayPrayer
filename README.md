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

### Option 1: installation Docker Compose (recommandee)

```bash
docker compose --profile dev run --rm setup
```

Cette commande lance `composer run setup` dans le container `setup` et execute automatiquement:

1. `composer install`
2. creation de `.env` depuis `.env.example` (si absent)
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

### Option 2: installation manuelle (hors Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

## Demarrage en developpement

Commande recommandee (tout-en-un via Docker Compose):

```bash
docker compose --profile dev up -d --build dev scheduler
```

Le service `dev` lance `composer run dev` dans Docker et demarre en parallele:

- serveur Laravel
- worker de queue (`queue:listen`)
- logs Laravel (`pail`)
- Vite en mode dev

Alternative hors Docker:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

## Docker Compose (dev et prod)

Le projet inclut un `docker-compose.yml` avec profils:

- `dev`: setup, dev, scheduler, postgres
- `legacy-dev`: app, queue, scheduler, vite, pail, postgres
- `prod`: app-prod, queue-prod, scheduler-prod, postgres

### Demarrage dev

```bash
docker compose --profile dev run --rm setup
docker compose --profile dev up -d --build dev scheduler
```

Acces:

- application: `http://localhost:8000`
- vite client: `http://localhost:5173/@vite/client`

### Demarrage prod (local)

1. creer le fichier d'environnement prod:

```bash
cp .env.production.example .env.production
```

2. renseigner au minimum `APP_KEY` et `DB_PASSWORD` dans `.env.production`

3. lancer la stack prod:

```bash
docker compose --profile prod up -d --build
```

### Commandes utiles

```bash
docker compose logs -f
docker compose ps
docker compose down
```

Note: ne pas lancer `dev` et `prod` en meme temps (conflit de port 8000).

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

Pour Render, le deploiement n'utilise pas `composer run setup` ni `composer run dev`.
Render utilise son propre pipeline (`buildCommand` + `startCommand`): le build lance l'installation Composer, la creation de `.env` si besoin, `php artisan key:generate`, `npm install` et `npm run build`, puis le demarrage lance `php artisan migrate --force` avant le serveur web.

Checklist rapide avant deploiement:

1. `php artisan test`
2. `npm run build`
3. verifier les variables d'environnement de prod (`APP_KEY`, `APP_URL`, `DB_*`)
4. verifier qu'aucun fichier local sensible n'est versionne (`.env`)

Le demarrage en environnement cible applique les migrations puis lance Laravel sur le port `10000`.

## Depannage rapide

### Ports deja utilises

Si `composer run dev` monte sur un autre port (8001/8002 ou 5174/5175), arreter les processus existants puis relancer:

```bash
p8000=$(lsof -ti tcp:8000 -sTCP:LISTEN); [ -n "$p8000" ] && kill $p8000
p5173=$(lsof -ti tcp:5173 -sTCP:LISTEN); [ -n "$p5173" ] && kill $p5173
composer run dev
```

### Erreur permission dans node_modules/.bin

```bash
chmod -R u+x node_modules/.bin
npm install
composer run dev
```

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
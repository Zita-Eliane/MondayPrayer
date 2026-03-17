# MondayPrayer

Application Laravel de suivi des jeûnes, sessions de prière, notifications et statistiques.

## Déploiement sur Render

Le dépôt contient déjà une configuration de base dans [render.yaml](render.yaml).

### 1. Créer les services Render

Créer au minimum :

- un service Web relié à ce dépôt
- une base PostgreSQL Render

### 2. Variables d'environnement à configurer

Configurer dans Render :

- `APP_NAME=MondayPrayer`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://votre-domaine.onrender.com`
- `APP_KEY=base64:...`
- `DB_CONNECTION=pgsql`
- `DB_HOST=...`
- `DB_PORT=5432`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=sync`
- `MAIL_MAILER=log` ou votre configuration SMTP

Pour générer une clé d'application :

```bash
openssl rand -base64 32
```

Puis préfixer la valeur avec `base64:`.

### 3. Commandes utilisées par Render

Build :

```bash
composer install --no-dev --optimize-autoloader && npm install && npm run build
```

Start :

```bash
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
```

### 4. Scheduler Laravel

L'application planifie les rappels de jeûne via le scheduler Laravel.

Créer un Cron Job Render qui exécute chaque minute :

```bash
php artisan schedule:run
```

La tâche métier planifiée est définie dans [routes/console.php](routes/console.php).

### 5. Notes de production

- Les rappels et statistiques utilisent désormais les colonnes `fast_date` et `participant_user_id`.
- `QUEUE_CONNECTION=sync` est recommandé pour un premier déploiement simple sans worker séparé.
- Si vous activez une vraie file asynchrone plus tard, il faudra ajouter un worker dédié.

## Incident rencontré sur Render (build Docker)

### Problème observé

Le build échouait pendant `composer install` dans l'image Docker avec l'erreur:

`Could not scan for classes inside "/var/www/html/vendor/symfony/polyfill-php84/Resources/stubs"`

### Cause probable

- Le projet utilisait un fichier `dockerignore` (sans point) au lieu de `.dockerignore`.
- En conséquence, `vendor/` local pouvait être copié dans l'image via `COPY . .`.
- Le contenu de `vendor/` présent dans le contexte de build entrait en conflit avec la résolution Composer dans le conteneur.
- L'option `--optimize-autoloader` pendant `composer install` rendait l'échec plus visible au moment de la génération de l'autoload optimisé.

### Solution appliquée

- Ajout d'un vrai fichier [.dockerignore](.dockerignore) pour exclure notamment `vendor/` et `node_modules/`.
- Durcissement de [Dockerfile](Dockerfile):
	- nettoyage préventif `vendor` et `node_modules` dans l'image avant installation,
	- `composer install` avec `--prefer-dist --no-progress --no-interaction` (sans `--optimize-autoloader`),
	- commande de démarrage alignée Render avec port dynamique `${PORT:-10000}`.

### Résultat attendu

Le build Docker Render doit passer de manière stable, sans dépendre d'un éventuel `vendor/` local.

## Swagger (OpenAPI)

Swagger est configuré avec L5-Swagger et une route API de test.

### Endpoints

- `GET /api/health` : endpoint de disponibilité API.

### Installation locale

1. Installer les dépendances Composer.
2. Générer la configuration Swagger:

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

3. Générer la documentation:

```bash
php artisan l5-swagger:generate
```

4. Ouvrir l'interface:

- `/api/documentation`

### Notes

- Les annotations globales OpenAPI sont dans `app/OpenApi/OpenApiSpec.php`.
- L'exemple d'endpoint annoté est dans `app/Http/Controllers/Api/HealthController.php`.
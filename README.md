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
# Guide de déploiement eCOMPTA — Hostinger Shared Hosting

## Prérequis

- Hébergement Hostinger Business ou supérieur (PHP 8.3)
- Accès hPanel (panneau de contrôle)
- Accès SSH (Hostinger → Advanced → SSH)
- Domaine configuré (ex: ecomptas.votredomaine.com)
- Base de données MySQL créée dans hPanel

---

## 1. Préparer la base de données MySQL

Dans hPanel → **Databases → MySQL Databases** :

```
Database name : ecomptas_db
Username      : ecomptas_user
Password      : (mot de passe fort)
```

Notez le **host MySQL** (généralement `localhost` ou `mysql.hostinger.com`).

---

## 2. Upload des fichiers via Git (SSH)

```bash
# Se connecter en SSH
ssh u123456789@srv.hostinger.com -p 65002

# Cloner le dépôt dans le répertoire home
cd ~
git clone https://github.com/votre-org/ecomptas.git ecomptas

# OU uploader via FTP le contenu du dossier /public dans public_html
# et le reste dans ~/ecomptas/
```

### Structure recommandée sur Hostinger :

```
~/
├── ecomptas/           ← racine Laravel (app/, config/, etc.)
│   ├── public/         ← SYMLINK ou contenu copié vers public_html/
│   └── storage/
└── public_html/        ← répertoire web Hostinger
    ├── index.php       ← modifié pour pointer vers ~/ecomptas
    └── .htaccess
```

### Modifier `public/index.php` pour pointer vers la bonne racine :

```php
// Remplacer les chemins relatifs par des chemins absolus
require __DIR__.'/../ecomptas/vendor/autoload.php';
$app = require_once __DIR__.'/../ecomptas/bootstrap/app.php';
```

**OU** utiliser la méthode `.htaccess` (recommandée) :

```
# Dans ~/public_html/.htaccess
RewriteEngine On
RewriteRule ^(.*)$ /ecomptas/public/$1 [L]
```

---

## 3. Configurer l'environnement

```bash
cd ~/ecomptas

# Copier et éditer .env
cp .env.example .env
nano .env
```

### Variables à renseigner dans `.env` :

```env
APP_NAME="eCOMPTA"
APP_ENV=production
APP_KEY=                          # Généré ci-dessous
APP_DEBUG=false
APP_URL=https://ecomptas.votredomaine.com

# MySQL Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_ecomptas
DB_USERNAME=u123456789_ecomptas
DB_PASSWORD=VotreMotDePasseMySQL

# Mail Hostinger SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@votredomaine.com
MAIL_PASSWORD=VotreMotDePasseMail
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@votredomaine.com
MAIL_FROM_NAME="eCOMPTA"

# Queue (database — pas Redis)
QUEUE_CONNECTION=database

# Storage local
FILESYSTEM_DISK=local

# n8n.cloud
N8N_WEBHOOK_URL=https://votre-instance.app.n8n.cloud/webhook/VOTRE-UUID
N8N_API_TOKEN=votre-bearer-token-n8n
N8N_SECRET=votre-secret-hmac-32-caracteres-minimum
N8N_CALLBACK_URL=https://ecomptas.votredomaine.com/webhooks/n8n/callback

# FeexPay
FEEXPAY_TOKEN=votre-token-feexpay
FEEXPAY_CALLBACK_URL=https://ecomptas.votredomaine.com/webhooks/feexpay
```

---

## 4. Installation des dépendances et migrations

```bash
cd ~/ecomptas

# Installer Composer (si non disponible)
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader

# Générer la clé d'application
php artisan key:generate

# Créer les tables
php artisan migrate --force

# Peupler les données initiales (plans + comptes SYSCOHADA)
php artisan db:seed --force

# Créer le lien symbolique storage (optionnel, PDF servis via Laravel)
php artisan storage:link

# Caches production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 5. Permissions des dossiers

```bash
chmod -R 775 ~/ecomptas/storage
chmod -R 775 ~/ecomptas/bootstrap/cache
find ~/ecomptas/storage -type d -exec chmod 775 {} \;
find ~/ecomptas/storage -type f -exec chmod 664 {} \;

# Créer les répertoires storage nécessaires
mkdir -p ~/ecomptas/storage/app/private/tenants
mkdir -p ~/ecomptas/storage/logs
mkdir -p ~/ecomptas/storage/framework/cache/data
mkdir -p ~/ecomptas/storage/framework/sessions
mkdir -p ~/ecomptas/storage/framework/views
```

---

## 6. Cron Jobs hPanel

Dans **hPanel → Advanced → Cron Jobs**, ajouter :

### Queue Worker (toutes les minutes)
```
* * * * * php ~/ecomptas/artisan queue:work --stop-when-empty --max-jobs=5 --max-time=55 --queue=default 2>&1
```

### Expiration des abonnements (tous les jours à minuit)
```
5 0 * * * php ~/ecomptas/artisan abonnements:expirer 2>&1
```

> **Important** : Sur Hostinger shared hosting, les crons s'exécutent au minimum toutes les minutes. Le paramètre `--max-time=55` garantit que le processus se termine avant le prochain déclenchement.

---

## 7. Configuration du domaine et SSL

1. Dans hPanel → **Domains**, pointer votre domaine vers `public_html/`
2. Activer **SSL/TLS** (Let's Encrypt gratuit dans hPanel)
3. Forcer HTTPS dans `.htaccess` :

```apache
# Dans public_html/.htaccess, AVANT les règles Laravel
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 8. Créer le super admin (iCODE)

```bash
php ~/ecomptas/artisan tinker
```

```php
// Dans tinker
\App\Models\User::create([
    'name'      => 'Super Admin iCODE',
    'email'     => 'admin@icode.bj',
    'password'  => \Hash::make('VotreMotDePasseSecurise!'),
    'role'      => 'super_admin',
    'tenant_id' => null,
]);
```

---

## 9. Configuration n8n.cloud

### Dans votre workflow n8n.cloud :

1. **Webhook trigger** : Recevoir les callbacks depuis Laravel
   - URL de callback : `POST https://ecomptas.votredomaine.com/webhooks/n8n/callback`
   - Header à envoyer : `X-N8N-Secret: votre-secret-hmac`

2. **HTTP Request node** : Récupérer le PDF facture
   - URL : `https://ecomptas.votredomaine.com/factures/{id}/pdf?token={encrypted_token}`
   - Token valide 30 minutes (généré par N8nService::declencherTraitement)

3. **Variables d'environnement n8n** :
   ```
   LARAVEL_CALLBACK_URL=https://ecomptas.votredomaine.com/webhooks/n8n/callback
   LARAVEL_N8N_SECRET=le-meme-secret-que-dans-env-laravel
   ```

---

## 10. Vérification post-déploiement

```bash
# Vérifier les logs
tail -f ~/ecomptas/storage/logs/laravel.log

# Tester les routes
curl -I https://ecomptas.votredomaine.com
curl -I https://ecomptas.votredomaine.com/login

# Vérifier la queue
php ~/ecomptas/artisan queue:monitor default

# Lancer un traitement de queue manuellement
php ~/ecomptas/artisan queue:work --once -v
```

---

## 11. Mise à jour (déploiement continu)

```bash
cd ~/ecomptas

# Activer mode maintenance
php artisan down --retry=60

# Tirer les changements
git pull origin main

# Mettre à jour les dépendances
composer install --no-dev --optimize-autoloader

# Migrations
php artisan migrate --force

# Vider et reconstruire les caches
php artisan optimize:clear
php artisan optimize

# Désactiver mode maintenance
php artisan up
```

---

## Secrets GitHub Actions (pour CI/CD automatique)

Dans votre repo GitHub → **Settings → Secrets and Variables → Actions** :

| Secret | Description |
|--------|-------------|
| `HOSTINGER_SSH_HOST` | IP ou domaine SSH Hostinger |
| `HOSTINGER_SSH_USER` | Nom d'utilisateur SSH |
| `HOSTINGER_SSH_KEY` | Clé privée SSH (PEM) |
| `HOSTINGER_SSH_PORT` | Port SSH (ex: 65002) |
| `HOSTINGER_APP_PATH` | Chemin absolu (ex: `/home/u123456789/ecomptas`) |
| `MAINTENANCE_SECRET` | Secret mode maintenance |

---

## Dépannage

### Erreur 500 au premier démarrage
```bash
php artisan config:clear
chmod -R 775 storage bootstrap/cache
```

### Queue ne traite pas les jobs
```bash
# Vérifier que la table jobs existe
php artisan migrate --force

# Lancer manuellement
php artisan queue:work --once -v
```

### PDF non accessible
- Vérifier que `storage/app/private/` existe et est accessible par PHP
- Vérifier le token dans l'URL (validité 30 min)

### Factures bloquées en "traitement_en_cours"
```bash
# Voir les jobs en attente
php artisan tinker
\Illuminate\Support\Facades\DB::table('jobs')->count();
\Illuminate\Support\Facades\DB::table('failed_jobs')->get();
```

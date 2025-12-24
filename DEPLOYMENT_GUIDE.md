# Guide de Déploiement - InfinityFree

## 📦 Étapes de déploiement

### 1. Préparer les fichiers localement

```bash
# Compiler les assets CSS/JS
npm run build

# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Créer une archive ZIP

Créez un fichier ZIP contenant TOUS les fichiers du projet SAUF :
- `/node_modules/`
- `/.git/`
- `/storage/logs/*.log`
- `/.env` (vous allez le créer sur le serveur)

### 3. Configuration InfinityFree

#### A. Accéder au panneau de contrôle
1. Connectez-vous à InfinityFree
2. Allez dans "Control Panel"
3. Cliquez sur "File Manager"

#### B. Structure des dossiers sur InfinityFree
```
htdocs/
├── .htaccess           (redirige vers public/)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── build/
├── resources/
├── routes/
├── storage/
└── vendor/
```

#### C. Uploader les fichiers
1. Dans File Manager, allez dans le dossier `htdocs`
2. Supprimez tous les fichiers par défaut
3. Uploadez votre archive ZIP
4. Extrayez l'archive dans `htdocs`

### 4. Créer la base de données

1. Dans le panneau InfinityFree, allez dans "MySQL Databases"
2. Créez une nouvelle base de données
3. Notez ces informations :
   - **Database Name** : (ex: epiz_123456_benka)
   - **Database User** : (ex: epiz_123456)
   - **Database Password** : (votre mot de passe)
   - **Database Host** : sql123.epizy.com

### 5. Configurer .env

1. Dans File Manager, créez un fichier `.env` à la racine du projet
2. Copiez le contenu de `.env.example`
3. Modifiez les valeurs suivantes :

```env
APP_NAME=Benka
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_ICI
APP_DEBUG=false
APP_URL=https://votre-site.epizy.com

DB_CONNECTION=mysql
DB_HOST=sql123.epizy.com
DB_PORT=3306
DB_DATABASE=epiz_123456_benka
DB_USERNAME=epiz_123456
DB_PASSWORD=votre_mot_de_passe

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 6. Générer la clé d'application

InfinityFree ne permet pas d'exécuter `php artisan` directement.
Générez la clé localement :

```bash
php artisan key:generate --show
```

Copiez la clé générée dans votre `.env` sur le serveur.

### 7. Configurer les permissions

Dans File Manager, configurez les permissions :
- `storage/` : 755
- `storage/framework/` : 755
- `storage/logs/` : 755
- `bootstrap/cache/` : 755

### 8. Importer la base de données

#### Option A : Via phpMyAdmin
1. Exportez votre base de données locale :
```bash
php artisan migrate --force
mysqldump -u root votre_db > database.sql
```

2. Dans InfinityFree, allez dans "phpMyAdmin"
3. Sélectionnez votre base de données
4. Cliquez sur "Import"
5. Uploadez `database.sql`

#### Option B : Créer les tables manuellement
Si vous avez peu de données, recréez manuellement via phpMyAdmin.

### 9. Tester le site

1. Visitez `https://votre-site.epizy.com`
2. Testez la connexion
3. Vérifiez que tout fonctionne

## 🔧 Problèmes courants

### Erreur 500
- Vérifiez les permissions de `storage/` et `bootstrap/cache/`
- Vérifiez que `.env` est correctement configuré
- Regardez les logs dans `storage/logs/`

### CSS/JS ne se chargent pas
- Vérifiez que le dossier `public/build/` a été uploadé
- Vérifiez les chemins dans le code (pas de chemins absolus)

### Erreur de connexion base de données
- Vérifiez les informations dans `.env`
- Assurez-vous que la base de données a été créée
- Vérifiez que l'utilisateur a les permissions

### Sessions ne fonctionnent pas
- Vérifiez que `storage/framework/sessions/` existe et a les bonnes permissions

## 📝 Limitations InfinityFree

- **Pas de cron jobs** : Les tâches planifiées ne fonctionneront pas
- **Pas de SSH** : Pas d'accès terminal
- **Pas de Composer** : Installez les dépendances localement avant d'uploader
- **Stockage limité** : 5GB max
- **Performance** : Limitée (serveur partagé)

## 🔄 Mises à jour futures

Pour mettre à jour votre application :
1. Modifiez le code localement
2. Compilez les assets : `npm run build`
3. Uploadez uniquement les fichiers modifiés via FTP/File Manager
4. Ne réuploadez pas tout à chaque fois

## ✅ Checklist finale

- [ ] Fichiers uploadés dans `htdocs/`
- [ ] `.htaccess` à la racine créé
- [ ] Base de données créée
- [ ] `.env` configuré avec les bonnes valeurs
- [ ] APP_KEY générée
- [ ] Permissions configurées (755 pour storage/)
- [ ] Base de données importée
- [ ] Site accessible via le navigateur
- [ ] Connexion fonctionne
- [ ] Toutes les pages se chargent correctement

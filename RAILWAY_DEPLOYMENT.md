# 🚂 Guide de Déploiement - Railway.app

## ✅ Fichiers préparés :
- ✓ `Procfile` - Commande de démarrage
- ✓ `nixpacks.toml` - Configuration build
- ✓ `.htaccess` - Redirections
- ✓ `APP_KEY` générée : `base64:/KDHSGdOg56qgWY/nZx+X39iB5soIcMJyvPvsVMNCvk=`

## 📋 Étapes de déploiement

### 1. Créer un compte Railway

1. Allez sur [railway.app](https://railway.app)
2. Cliquez sur "Start a New Project"
3. Connectez-vous avec GitHub (recommandé)
4. Pas besoin de carte bancaire pour commencer

### 2. Pousser votre code sur GitHub

Si vous n'avez pas encore de repo GitHub :

```bash
# Initialiser git (si pas déjà fait)
git init

# Ajouter tous les fichiers
git add .

# Créer le premier commit
git commit -m "Initial commit - Benka presence app"

# Créer un nouveau repo sur GitHub.com
# Puis lier votre repo local :
git remote add origin https://github.com/VOTRE_USERNAME/benka.git
git branch -M main
git push -u origin main
```

### 3. Créer le projet sur Railway

1. Dans Railway, cliquez sur "New Project"
2. Sélectionnez "Deploy from GitHub repo"
3. Autorisez Railway à accéder à GitHub
4. Sélectionnez votre repo `benka`
5. Railway détecte automatiquement Laravel !

### 4. Ajouter la base de données MySQL

1. Dans votre projet Railway, cliquez sur "+ New"
2. Sélectionnez "Database"
3. Choisissez "MySQL"
4. Railway crée automatiquement la base de données

### 5. Configurer les variables d'environnement

Dans Railway, allez dans votre service web → "Variables" :

```env
APP_NAME=Benka
APP_ENV=production
APP_KEY=base64:/KDHSGdOg56qgWY/nZx+X39iB5soIcMJyvPvsVMNCvk=
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**Note importante** : Railway injecte automatiquement les variables MySQL (`MYSQLHOST`, `MYSQLPORT`, etc.) quand vous ajoutez une base de données MySQL.

### 6. Générer un domaine public

1. Dans Railway, allez dans "Settings"
2. Sous "Domains", cliquez sur "Generate Domain"
3. Vous obtenez une URL gratuite : `benka-production.up.railway.app`

### 7. Déploiement automatique

Railway déploie automatiquement dès que vous poussez sur GitHub !

```bash
# Faire des modifications
git add .
git commit -m "Update: nouvelle fonctionnalité"
git push

# Railway redéploie automatiquement !
```

## 🔧 Configuration avancée

### Modifier le build command (optionnel)

Si Railway ne build pas correctement, vous pouvez forcer les commandes :

**Settings → Build Command :**
```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build
```

**Settings → Start Command :**
```bash
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

### Permissions de stockage

Railway configure automatiquement les permissions pour `storage/` et `bootstrap/cache/`.

## 📊 Vérifier le déploiement

1. **Logs en temps réel** : Cliquez sur "Deployments" → "View Logs"
2. **Test du site** : Visitez votre domaine Railway
3. **Base de données** : Vérifiez que les migrations ont fonctionné

## 🐛 Problèmes courants

### Erreur 500 - Internal Server Error

**Vérifier les logs :**
```bash
# Dans Railway, allez dans "Deployments" → "View Logs"
```

**Solutions :**
- Vérifiez que `APP_KEY` est définie
- Vérifiez que les variables MySQL sont correctes
- Vérifiez que les migrations ont réussi

### CSS/JS ne se chargent pas

**Problème :** Les assets ne sont pas compilés

**Solution :**
1. Vérifiez que `npm run build` est dans le build command
2. Vérifiez que le dossier `public/build/` est généré
3. Redéployez le projet

### Base de données ne se connecte pas

**Problème :** Variables d'environnement incorrectes

**Solution :**
1. Vérifiez que vous utilisez `${{MYSQLHOST}}` et non des valeurs en dur
2. Railway remplace automatiquement ces variables
3. Redémarrez le service

### Migrations échouent

**Problème :** La base de données n'est pas accessible pendant le build

**Solution :**
- Les migrations doivent être dans la commande de démarrage (déjà configuré dans `Procfile`)
- Elles s'exécutent APRÈS que la base de données soit prête

## 💰 Limites du plan gratuit

Railway offre **5$ de crédit gratuit par mois**, ce qui équivaut à :
- ~500 heures de serveur actif/mois
- Largement suffisant pour une petite équipe
- Pas besoin de carte bancaire au début

**Votre application consommera environ 2-3$/mois avec usage normal**

## 🔄 Mises à jour

Pour mettre à jour votre application :

```bash
# 1. Faire vos modifications localement
# 2. Tester localement
php artisan serve

# 3. Commit et push
git add .
git commit -m "Fix: correction du bug XYZ"
git push

# Railway redéploie automatiquement en ~2-3 minutes
```

## ✅ Checklist finale

- [ ] Compte Railway créé
- [ ] Projet sur GitHub
- [ ] Projet Railway créé et connecté à GitHub
- [ ] Base de données MySQL ajoutée
- [ ] Variables d'environnement configurées
- [ ] APP_KEY définie
- [ ] Domaine généré
- [ ] Premier déploiement réussi
- [ ] Migrations exécutées
- [ ] Site accessible en ligne
- [ ] Connexion utilisateur fonctionne
- [ ] CSS/JS se chargent correctement

## 🎉 Félicitations !

Votre application Benka est maintenant en ligne sur Railway !

**URL de votre app** : `https://votre-nom-projet.up.railway.app`

## 📞 Support

- Documentation Railway : [docs.railway.app](https://docs.railway.app)
- Discord Railway : [railway.app/discord](https://railway.app/discord)
- Moi pour vous aider ! 😊

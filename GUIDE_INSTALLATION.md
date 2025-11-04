# 🚀 Guide d'Installation - Hotel Transport Management System

## Table des Matières

1. [Prérequis](#prérequis)
2. [Installation Rapide](#installation-rapide)
3. [Installation Détaillée](#installation-détaillée)
4. [Configuration](#configuration)
5. [Lancement](#lancement)
6. [Vérification](#vérification)
7. [Dépannage](#dépannage)

---

## Prérequis

### Système d'Exploitation
- Linux (Ubuntu 20.04+ recommandé)
- macOS 10.15+
- Windows 10/11 avec WSL2

### Logiciels Requis

#### 1. Docker & Docker Compose
```bash
# Vérifier l'installation
docker --version
docker-compose --version

# Si non installé (Linux):
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

#### 2. Git
```bash
# Vérifier l'installation
git --version

# Si non installé:
sudo apt-get update
sudo apt-get install git
```

### Configuration Minimale

- **RAM**: 8 GB minimum (16 GB recommandé)
- **CPU**: 4 cores minimum
- **Disque**: 20 GB d'espace libre
- **Réseau**: Connexion Internet pour télécharger les images Docker

---

## Installation Rapide

Pour une installation rapide en 5 minutes :

```bash
# 1. Cloner le projet
git clone https://github.com/votre-org/hotel-transport-management.git
cd hotel-transport-management

# 2. Copier le fichier d'environnement
cp .env.example .env

# 3. Lancer Docker Compose
docker-compose up -d

# 4. Attendre 2-3 minutes que tous les services démarrent

# 5. Initialiser la base de données (si nécessaire)
docker exec -it hotel_transport_laravel php artisan migrate --seed

# 6. Ouvrir le navigateur
# Landing Page: http://localhost
# Client: http://localhost:3000
# Driver: http://localhost:4200
# Admin: http://localhost:3002
```

**Compte admin par défaut :**
- Email: `admin@hoteltransport.ma`
- Password: `Admin@123`

---

## Installation Détaillée

### Étape 1: Cloner le Projet

```bash
# Via HTTPS
git clone https://github.com/votre-org/hotel-transport-management.git

# Via SSH (si configuré)
git clone git@github.com:votre-org/hotel-transport-management.git

# Entrer dans le dossier
cd hotel-transport-management
```

### Étape 2: Configuration de l'Environnement

```bash
# Copier le fichier exemple
cp .env.example .env

# Éditer le fichier .env
nano .env  # ou vim, code, etc.
```

#### Configuration Minimale Requise

Modifier les valeurs suivantes dans `.env` :

```env
# Base de données MySQL
DB_PASSWORD=votre_mot_de_passe_securise
DB_ROOT_PASSWORD=votre_root_password_securise

# MongoDB
MONGO_PASSWORD=votre_mongo_password_securise

# JWT Secret (minimum 32 caractères)
JWT_SECRET=votre_secret_jwt_tres_long_et_securise_ici

# Email (optionnel pour le développement)
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_app_password

# SMS (optionnel)
SMS_API_KEY=votre_cle_api_sms
```

### Étape 3: Vérifier Docker Compose

```bash
# Vérifier la configuration
docker-compose config

# Si des erreurs apparaissent, vérifier le fichier docker-compose.yml
```

### Étape 4: Télécharger les Images Docker

```bash
# Télécharger toutes les images
docker-compose pull

# Cela peut prendre 10-15 minutes selon votre connexion
```

### Étape 5: Build des Images Personnalisées

```bash
# Construire les images
docker-compose build

# Temps estimé: 5-10 minutes
```

### Étape 6: Lancer les Containers

```bash
# Démarrer tous les services en arrière-plan
docker-compose up -d

# Voir les logs en temps réel
docker-compose logs -f

# Attendre que tous les services soient "healthy"
# MySQL peut prendre 30-60 secondes pour s'initialiser
```

### Étape 7: Vérifier l'État des Services

```bash
# Voir tous les containers
docker-compose ps

# Tous les containers doivent avoir le statut "Up"
```

### Étape 8: Initialiser les Bases de Données

#### MySQL (Laravel)

```bash
# Entrer dans le container Laravel
docker exec -it hotel_transport_laravel bash

# Installer les dépendances Composer
composer install

# Générer la clé de l'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate

# Peupler la base de données avec les données de test
php artisan db:seed

# Sortir du container
exit
```

#### MongoDB (Node.js)

```bash
# Entrer dans le container Node.js
docker exec -it hotel_transport_nodejs bash

# Installer les dépendances NPM
npm install

# Initialiser MongoDB (si script disponible)
npm run init:db

# Sortir
exit
```

### Étape 9: Configuration des Frontends

#### React (Client Portal)

```bash
# Entrer dans le container React
docker exec -it hotel_transport_react bash

# Installer les dépendances
npm install

# (Le serveur de développement démarre automatiquement)
exit
```

#### Angular (Driver Portal)

```bash
# Entrer dans le container Angular
docker exec -it hotel_transport_angular bash

# Installer les dépendances
npm install

# (Le serveur de développement démarre automatiquement)
exit
```

#### Next.js (Admin Portal)

```bash
# Entrer dans le container Next.js
docker exec -it hotel_transport_nextjs bash

# Installer les dépendances
npm install

# Build pour la première fois
npm run build

exit
```

---

## Configuration

### Ports Utilisés

Assurez-vous que ces ports sont disponibles :

| Service | Port | URL |
|---------|------|-----|
| Nginx | 80, 443 | http://localhost |
| Laravel API | 8000 | http://localhost:8000 |
| Node.js API | 3001 | http://localhost:3001 |
| PHP Apache | 8080 | http://localhost:8080 |
| React Client | 3000 | http://localhost:3000 |
| Angular Driver | 4200 | http://localhost:4200 |
| Next.js Admin | 3002 | http://localhost:3002 |
| MySQL | 3306 | localhost:3306 |
| MongoDB | 27017 | localhost:27017 |
| Redis | 6379 | localhost:6379 |
| N8N | 5678 | http://localhost:5678 |

### Vérifier les Ports

```bash
# Linux/Mac
sudo lsof -i :80
sudo lsof -i :3000
# etc.

# Windows
netstat -ano | findstr :80
netstat -ano | findstr :3000
```

Si un port est occupé, vous pouvez :
1. Arrêter le service qui l'utilise
2. Ou modifier le port dans `docker-compose.yml`

---

## Lancement

### Démarrer Tous les Services

```bash
docker-compose up -d
```

### Démarrer un Service Spécifique

```bash
docker-compose up -d mysql
docker-compose up -d laravel
docker-compose up -d react_client
```

### Voir les Logs

```bash
# Tous les services
docker-compose logs -f

# Un service spécifique
docker-compose logs -f laravel
docker-compose logs -f nodejs
```

### Arrêter les Services

```bash
# Arrêter tous les services
docker-compose stop

# Arrêter et supprimer les containers
docker-compose down

# Arrêter et supprimer les containers + volumes
docker-compose down -v
```

### Redémarrer un Service

```bash
docker-compose restart laravel
docker-compose restart nodejs
```

---

## Vérification

### 1. Vérifier que Tous les Services Fonctionnent

```bash
docker-compose ps

# Tous doivent avoir le statut "Up"
```

### 2. Tester les URLs

#### Landing Page
```bash
curl http://localhost
# Devrait retourner du HTML
```

#### Laravel API
```bash
curl http://localhost:8000/api/health
# Devrait retourner: {"status":"ok"}
```

#### Node.js API
```bash
curl http://localhost:3001/api/health
# Devrait retourner: {"status":"ok"}
```

### 3. Tester la Base de Données

```bash
# MySQL
docker exec -it hotel_transport_mysql mysql -u hotel_user -p hotel_transport
# Entrer le mot de passe

# Vérifier les tables
SHOW TABLES;
exit;

# MongoDB
docker exec -it hotel_transport_mongodb mongo -u mongo_admin -p
# Entrer le mot de passe
show dbs;
exit;
```

### 4. Accéder aux Interfaces Web

Ouvrir dans le navigateur :

1. **Landing Page**: http://localhost
2. **Client Portal**: http://localhost:3000
3. **Driver Portal**: http://localhost:4200
4. **Admin Portal**: http://localhost:3002
5. **N8N**: http://localhost:5678

### 5. Se Connecter au Portail Admin

- URL: http://localhost:3002
- Email: `admin@hoteltransport.ma`
- Password: `Admin@123`

---

## Dépannage

### Problème: Les containers ne démarrent pas

```bash
# Voir les erreurs
docker-compose logs

# Vérifier l'espace disque
df -h

# Nettoyer Docker
docker system prune -a
```

### Problème: Port déjà utilisé

```bash
# Identifier le processus
sudo lsof -i :PORT_NUMBER

# Tuer le processus
sudo kill -9 PID

# Ou modifier le port dans docker-compose.yml
```

### Problème: Erreur de connexion à la base de données

```bash
# Vérifier que MySQL est prêt
docker exec hotel_transport_mysql mysqladmin ping -h localhost

# Voir les logs MySQL
docker logs hotel_transport_mysql

# Redémarrer MySQL
docker-compose restart mysql

# Attendre 30 secondes et réessayer
```

### Problème: Erreur 502 Bad Gateway (Nginx)

```bash
# Vérifier que les backends sont actifs
docker-compose ps

# Voir les logs Nginx
docker logs hotel_transport_nginx

# Redémarrer Nginx
docker-compose restart nginx
```

### Problème: Frontend ne se charge pas

```bash
# React
docker logs hotel_transport_react
docker exec -it hotel_transport_react npm install
docker-compose restart react_client

# Angular
docker logs hotel_transport_angular
docker exec -it hotel_transport_angular npm install
docker-compose restart angular_driver

# Next.js
docker logs hotel_transport_nextjs
docker exec -it hotel_transport_nextjs npm install
docker-compose restart nextjs_admin
```

### Problème: Erreur "permission denied"

```bash
# Donner les permissions
sudo chown -R $USER:$USER .

# Ou exécuter avec sudo
sudo docker-compose up -d
```

### Problème: Composer/NPM install échoue

```bash
# Entrer dans le container
docker exec -it CONTAINER_NAME bash

# Nettoyer le cache
composer clear-cache  # Pour PHP
npm cache clean --force  # Pour Node.js

# Réinstaller
composer install
npm install
```

### Problème: Migrations échouent

```bash
# Vérifier la connexion à la DB
docker exec -it hotel_transport_laravel php artisan tinker
>>> DB::connection()->getPdo();

# Reset et migrer à nouveau
php artisan migrate:fresh --seed
```

### Réinitialisation Complète

Si rien ne fonctionne, réinitialisation complète :

```bash
# Arrêter et tout supprimer
docker-compose down -v

# Supprimer les images
docker-compose down --rmi all

# Nettoyer Docker
docker system prune -a -f

# Recommencer l'installation
docker-compose up -d --build
```

---

## Commandes Utiles

### Docker Compose

```bash
# Démarrer
docker-compose up -d

# Arrêter
docker-compose down

# Rebuild
docker-compose up -d --build

# Voir les logs
docker-compose logs -f [service]

# Voir les processus
docker-compose ps

# Exécuter une commande
docker-compose exec [service] [command]
```

### Laravel

```bash
docker exec -it hotel_transport_laravel php artisan migrate
docker exec -it hotel_transport_laravel php artisan db:seed
docker exec -it hotel_transport_laravel php artisan cache:clear
docker exec -it hotel_transport_laravel php artisan config:clear
docker exec -it hotel_transport_laravel php artisan route:list
```

### Node.js

```bash
docker exec -it hotel_transport_nodejs npm install
docker exec -it hotel_transport_nodejs npm run dev
docker exec -it hotel_transport_nodejs npm test
```

---

## Support

Si vous rencontrez des problèmes :

1. Vérifiez les logs : `docker-compose logs -f`
2. Consultez la documentation : `PROJECT_DOCUMENTATION.md`
3. Vérifiez les issues GitHub
4. Contactez le support : support@hoteltransport.ma

---

**Installation réussie !** 🎉

Vous pouvez maintenant commencer à utiliser le système.

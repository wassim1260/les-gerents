# Hotel Transport Management System - Documentation Complète

## 📋 Vue d'ensemble

Système complet de gestion de transport pour groupes hôteliers au Maroc. Solution full-stack professionnelle avec 3 portails distincts (Client, Chauffeur, Admin) utilisant toutes les technologies modernes.

### 🎯 Objectifs du Projet

- Gestion centralisée des demandes de transport
- Affectation intelligente des chauffeurs
- Suivi en temps réel des trajets
- Gestion complète de la flotte et des réservations
- Automatisation des workflows avec N8N
- Analytics et reporting avancés

## 🛠 Stack Technologique

### Frontend
- **React 18+** - Portail Client (interface moderne et responsive)
- **Angular 17+** - Portail Chauffeur (application mobile-first avec GPS)
- **Next.js 14+** - Portail Admin (dashboard analytics et SSR)
- **HTML5/CSS3/Bootstrap 5** - Landing page et pages statiques
- **JavaScript ES6+** - Scripts et utilitaires

### Backend
- **Laravel 10+** - API REST principale, authentification, gestion métier
- **Node.js/Express** - API temps réel, WebSocket, microservices
- **PHP 8.2+** - Scripts de traitement, compatibilité hébergement classique

### Bases de Données
- **MySQL 8.0** - Données transactionnelles (utilisateurs, réservations, véhicules)
- **MongoDB 7.0** - Logs, notifications, données temps réel

### DevOps & Infrastructure
- **Docker & Docker Compose** - Containerisation complète
- **Nginx** - Reverse proxy et load balancing
- **Redis** - Cache et gestion de sessions
- **N8N** - Automatisation des workflows

### APIs & Intégrations
- **RESTful API** - Architecture REST complète
- **WebSocket** - Notifications et suivi temps réel
- **JSON** - Format d'échange de données
- **JWT** - Authentification sécurisée

## 📁 Structure du Projet

```
hotel-transport-management/
│
├── docker/                          # Configuration Docker
│   ├── php/                         # Dockerfile PHP/Apache
│   ├── laravel/                     # Dockerfile Laravel
│   ├── nodejs/                      # Dockerfile Node.js
│   ├── react/                       # Dockerfile React
│   ├── angular/                     # Dockerfile Angular
│   ├── nextjs/                      # Dockerfile Next.js
│   └── nginx/                       # Configuration Nginx
│
├── backend/
│   ├── laravel/                     # Application Laravel
│   │   ├── app/
│   │   │   ├── Http/Controllers/   # Contrôleurs API
│   │   │   ├── Models/             # Modèles Eloquent
│   │   │   ├── Services/           # Services métier
│   │   │   └── Middleware/         # Middlewares
│   │   ├── database/
│   │   │   ├── migrations/         # Migrations
│   │   │   └── seeders/            # Seeders
│   │   └── routes/
│   │       └── api.php             # Routes API
│   │
│   ├── nodejs/                      # Application Node.js
│   │   ├── src/
│   │   │   ├── controllers/        # Contrôleurs
│   │   │   ├── models/             # Modèles MongoDB
│   │   │   ├── routes/             # Routes API
│   │   │   ├── services/           # Services
│   │   │   ├── websocket/          # WebSocket handlers
│   │   │   └── middleware/         # Middlewares
│   │   ├── config/                 # Configuration
│   │   └── server.js               # Point d'entrée
│   │
│   └── php/                         # Scripts PHP
│       ├── api/                     # APIs PHP
│       │   ├── auth.php            # Authentification
│       │   ├── bookings.php        # Réservations
│       │   └── ...
│       ├── config.php               # Configuration
│       └── utils.php                # Utilitaires
│
├── frontend/
│   ├── html/                        # Site statique
│   │   ├── index.html              # Landing page
│   │   ├── login.html              # Page de connexion
│   │   ├── register.html           # Page d'inscription
│   │   ├── css/
│   │   │   └── style.css           # Styles personnalisés
│   │   ├── js/
│   │   │   └── main.js             # Scripts principaux
│   │   └── images/                 # Images et assets
│   │
│   ├── react-client/                # Portail Client (React)
│   │   ├── src/
│   │   │   ├── components/         # Composants réutilisables
│   │   │   ├── pages/              # Pages
│   │   │   ├── services/           # Services API
│   │   │   ├── context/            # Context API
│   │   │   ├── hooks/              # Custom hooks
│   │   │   └── utils/              # Utilitaires
│   │   └── public/                 # Assets statiques
│   │
│   ├── angular-driver/              # Portail Chauffeur (Angular)
│   │   ├── src/
│   │   │   ├── app/
│   │   │   │   ├── components/     # Composants
│   │   │   │   ├── services/       # Services
│   │   │   │   ├── guards/         # Guards de navigation
│   │   │   │   ├── interceptors/   # HTTP interceptors
│   │   │   │   └── models/         # Modèles TypeScript
│   │   │   └── environments/       # Environnements
│   │   └── angular.json            # Configuration Angular
│   │
│   └── nextjs-admin/                # Portail Admin (Next.js)
│       ├── app/                     # App directory (Next.js 13+)
│       │   ├── dashboard/          # Dashboard
│       │   ├── bookings/           # Gestion réservations
│       │   ├── drivers/            # Gestion chauffeurs
│       │   ├── vehicles/           # Gestion véhicules
│       │   └── analytics/          # Analytics
│       ├── components/             # Composants React
│       ├── lib/                    # Utilitaires et helpers
│       └── public/                 # Assets statiques
│
├── database/
│   ├── mysql/
│   │   └── init.sql                # Script d'initialisation MySQL
│   └── mongodb/
│       └── init.js                 # Script d'initialisation MongoDB
│
├── docker-compose.yml               # Configuration Docker Compose
├── .env.example                     # Variables d'environnement exemple
├── README.md                        # Documentation README
└── PROJECT_DOCUMENTATION.md         # Ce fichier

```

## 🔐 Sécurité

### Authentification
- JWT (JSON Web Tokens) pour l'authentification API
- Sessions sécurisées avec HttpOnly cookies
- Hachage bcrypt pour les mots de passe
- Vérification email obligatoire
- Système de réinitialisation de mot de passe sécurisé

### Protection
- CORS configuré correctement
- Rate limiting sur les endpoints sensibles
- Validation et sanitization des entrées
- Protection XSS et CSRF
- Préparation des requêtes SQL (PDO)
- Headers de sécurité (HSTS, CSP, etc.)

## 📊 Base de Données

### MySQL - Schéma Principal

#### Tables Principales
1. **users** - Utilisateurs (clients, chauffeurs, admins)
2. **roles** - Rôles et permissions
3. **client_profiles** - Profils clients
4. **driver_profiles** - Profils chauffeurs
5. **cities** - Villes desservies (10 villes du Maroc)
6. **vehicles** - Flotte de véhicules
7. **vehicle_types** - Types de véhicules (Taxi, Van, Bus, Privé)
8. **luxury_categories** - Catégories de luxe (Standard, Confort, Luxe, VIP)
9. **service_types** - Types de services (Aéroport, Taxi, etc.)
10. **bookings** - Réservations
11. **booking_status_history** - Historique des statuts
12. **trip_tracking** - Suivi GPS temps réel
13. **payments** - Paiements
14. **reviews** - Évaluations
15. **notifications** - Notifications
16. **audit_logs** - Logs d'audit
17. **sessions** - Sessions utilisateurs

### MongoDB - Collections

1. **activity_logs** - Logs d'activité détaillés
2. **realtime_locations** - Positions GPS en temps réel
3. **notifications_queue** - File d'attente des notifications
4. **analytics_data** - Données analytics agrégées
5. **system_metrics** - Métriques système

## 🌍 Villes Couvertes

1. **Casablanca** (الدار البيضاء) - CAS
2. **Rabat** (الرباط) - RAB
3. **Tanger** (طنجة) - TNG
4. **Marrakech** (مراكش) - MAR
5. **Agadir** (أكادير) - AGA
6. **Al Hoceima** (الحسيمة) - AHU
7. **Dakhla** (الداخلة) - DAK
8. **Nador** (الناظور) - NDR
9. **Fès** (فاس) - FES
10. **Ouarzazate** (ورزازات) - OZZ

## 🚗 Services de Transport

### 1. Transfert Aéroport
- Navette depuis/vers l'aéroport
- Suivi des vols en temps réel
- Accueil personnalisé
- Réservation anticipée recommandée (3h minimum)

### 2. Taxi
- Service standard dans toutes les villes
- Disponible 24/7
- Réservation instantanée ou anticipée
- Tarification au compteur + distance

### 3. Van
- Transport de groupes (jusqu'à 8 passagers)
- Idéal pour familles ou petits groupes
- Véhicules spacieux et confortables
- Tarification avantageuse pour groupes

### 4. Bus
- Transport de grands groupes (jusqu'à 20 passagers)
- Pour événements, excursions
- Bus modernes et équipés
- Réservation minimum 6h à l'avance

### 5. Chauffeur Privé
- Service VIP premium
- Chauffeur professionnel en costume
- Véhicules de luxe
- Service personnalisé et discret

## 💎 Catégories de Véhicules

### Standard (x1.0)
- Climatisation
- Radio
- Véhicules confortables et fiables
- Prix de base

### Confort (x1.3)
- Tout Standard +
- WiFi gratuit
- Eau minérale
- Chargeurs USB

### Luxe (x1.8)
- Tout Confort +
- Sièges en cuir
- Snacks et rafraîchissements
- Ambiance personnalisée

### VIP (x2.5)
- Tout Luxe +
- Véhicules de prestige
- Champagne
- Service exclusif
- Chauffeur en costume

## 🔄 Workflow de Réservation

1. **Client** : Crée une demande de trajet via le portail
2. **Système** : Valide la demande et calcule le prix
3. **Admin** : Reçoit la notification et vérifie la demande
4. **Admin** : Sélectionne un chauffeur disponible et affecte le trajet
5. **Chauffeur** : Reçoit la notification et accepte le trajet
6. **Client** : Reçoit la confirmation avec les détails du chauffeur
7. **Chauffeur** : Se met en route (statut "en route")
8. **Client** : Suit le trajet en temps réel sur la carte
9. **Chauffeur** : Arrive sur place (statut "arrivé")
10. **Trajet** : En cours (suivi GPS actif)
11. **Trajet** : Terminé (demande d'évaluation)
12. **Client** : Laisse une évaluation
13. **Système** : Traite le paiement et envoie la facture

## 📱 Portails Utilisateurs

### Portail Client (React)
**URL**: http://localhost:3000 ou http://localhost/client

**Fonctionnalités**:
- Inscription et connexion
- Recherche et réservation de trajets
- Sélection du type de service et véhicule
- Calcul automatique du prix
- Suivi en temps réel des trajets
- Historique des réservations
- Gestion du profil
- Système de paiement
- Évaluations et avis
- Notifications en temps réel
- Support client

### Portail Chauffeur (Angular)
**URL**: http://localhost:4200 ou http://localhost/driver

**Fonctionnalités**:
- Connexion sécurisée (inscription par admin uniquement)
- Dashboard avec trajets assignés
- Acceptation/refus des trajets
- Navigation GPS intégrée
- Mise à jour du statut en temps réel
- Partage de position en direct
- Historique des courses
- Statistiques et revenus
- Gestion du profil
- Chat avec le client
- Disponibilité on/off
- Gestion des documents (permis, assurance)

### Portail Admin (Next.js)
**URL**: http://localhost:3002 ou http://localhost/admin

**Fonctionnalités**:
- Dashboard avec statistiques en temps réel
- Gestion des demandes de trajet
- Affectation intelligente des chauffeurs
- Gestion de la flotte de véhicules
- Inscription et gestion des chauffeurs
- Gestion des clients
- Suivi GPS de toute la flotte
- Analytics et rapports
- Gestion des tarifs
- Gestion des services
- Gestion des villes
- Configuration système
- Logs et audit trail
- Support et tickets
- Facturation et comptabilité

## 🔌 APIs

### API Laravel (Port 8000)
```
Base URL: http://localhost:8000/api

Authentification:
POST /auth/register
POST /auth/login
POST /auth/logout
POST /auth/refresh
POST /auth/forgot-password
POST /auth/reset-password

Utilisateurs:
GET    /users
GET    /users/{id}
PUT    /users/{id}
DELETE /users/{id}

Réservations:
GET    /bookings
POST   /bookings
GET    /bookings/{id}
PUT    /bookings/{id}
DELETE /bookings/{id}
POST   /bookings/{id}/assign
POST   /bookings/{id}/cancel

Chauffeurs:
GET    /drivers
POST   /drivers
GET    /drivers/{id}
PUT    /drivers/{id}
GET    /drivers/available

Véhicules:
GET    /vehicles
POST   /vehicles
GET    /vehicles/{id}
PUT    /vehicles/{id}
DELETE /vehicles/{id}

Services:
GET    /services
GET    /service-types
GET    /vehicle-types
GET    /luxury-categories

Villes:
GET    /cities
GET    /cities/{id}

Paiements:
POST   /payments
GET    /payments/{id}

Reviews:
POST   /reviews
GET    /reviews/driver/{driverId}
GET    /reviews/booking/{bookingId}
```

### API Node.js (Port 3001)
```
Base URL: http://localhost:3001/api

WebSocket:
WS /socket.io

Suivi GPS:
POST   /tracking/update
GET    /tracking/booking/{bookingId}
GET    /tracking/driver/{driverId}

Notifications:
GET    /notifications
POST   /notifications/send
PUT    /notifications/{id}/read
DELETE /notifications/{id}

Analytics:
GET    /analytics/dashboard
GET    /analytics/revenue
GET    /analytics/trips
GET    /analytics/drivers
```

### API PHP (Port 8080)
```
Base URL: http://localhost:8080

Scripts de traitement:
POST   /api/auth.php?action=register
POST   /api/auth.php?action=login
POST   /api/bookings.php?action=create
GET    /api/bookings.php?action=list
```

## 🔔 Système de Notifications

### Types de Notifications

1. **Email**
   - Confirmation de réservation
   - Affectation de chauffeur
   - Rappel de trajet
   - Facture
   - Mot de passe oublié

2. **SMS**
   - Confirmation de réservation
   - Chauffeur en route
   - Arrivée du chauffeur

3. **Push (In-app)**
   - Nouvelle réservation (Admin)
   - Affectation de trajet (Chauffeur)
   - Chauffeur assigné (Client)
   - Changement de statut

4. **WebSocket (Temps réel)**
   - Position du chauffeur
   - Mise à jour du statut
   - Messages de chat

## 🤖 Automatisation N8N

**URL**: http://localhost:5678

### Workflows Configurés

1. **Nouvelle Réservation**
   - Déclencheur: Nouvelle entrée dans bookings
   - Actions:
     - Envoyer email de confirmation au client
     - Notifier les admins
     - Créer une tâche d'affectation

2. **Affectation Chauffeur**
   - Déclencheur: Booking assigné
   - Actions:
     - Notifier le chauffeur (Email + SMS)
     - Notifier le client avec détails du chauffeur
     - Créer un rappel 1h avant le trajet

3. **Trajet Terminé**
   - Déclencheur: Statut = completed
   - Actions:
     - Demander évaluation au client
     - Générer et envoyer la facture
     - Mettre à jour les statistiques
     - Calculer les revenus du chauffeur

4. **Rappels et Alertes**
   - Permis de conduire expirant
   - Assurance véhicule
   - Contrôle technique
   - Entretien véhicule

## 🚀 Installation et Déploiement

### Prérequis

- Docker et Docker Compose
- Git
- 8 GB RAM minimum
- 20 GB espace disque

### Installation Locale

```bash
# Cloner le repository
git clone <repository-url>
cd hotel-transport-management

# Copier les variables d'environnement
cp .env.example .env

# Éditer .env avec vos configurations

# Lancer les containers Docker
docker-compose up -d

# Attendre que tous les services démarrent (2-3 minutes)

# Accéder aux différents portails:
# Landing Page: http://localhost
# Client Portal: http://localhost:3000
# Driver Portal: http://localhost:4200
# Admin Portal: http://localhost:3002
# N8N: http://localhost:5678
```

### Configuration Initiale

```bash
# Se connecter au container Laravel
docker exec -it hotel_transport_laravel bash

# Installer les dépendances
composer install

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate

# Exécuter les seeders
php artisan db:seed

# Sortir du container
exit
```

### Compte Admin par Défaut

```
Email: admin@hoteltransport.ma
Password: Admin@123
```

## 🧪 Tests

### Tests Backend Laravel
```bash
docker exec -it hotel_transport_laravel bash
php artisan test
```

### Tests Backend Node.js
```bash
docker exec -it hotel_transport_nodejs bash
npm test
```

### Tests Frontend React
```bash
docker exec -it hotel_transport_react bash
npm test
```

## 📈 Performance et Scaling

### Optimisations Implémentées

1. **Cache Redis**
   - Sessions utilisateurs
   - Résultats de requêtes fréquentes
   - Données de configuration

2. **Indexation Base de Données**
   - Index sur colonnes fréquemment utilisées
   - Index composites pour requêtes complexes

3. **CDN et Assets**
   - Assets statiques optimisés
   - Images compressées
   - Lazy loading

4. **API Response Caching**
   - Cache des endpoints publics
   - Invalidation intelligente

## 🔍 Monitoring et Logs

### Logs Disponibles

```bash
# Logs Laravel
docker logs hotel_transport_laravel

# Logs Node.js
docker logs hotel_transport_nodejs

# Logs Nginx
docker logs hotel_transport_nginx

# Logs MySQL
docker logs hotel_transport_mysql

# Logs MongoDB
docker logs hotel_transport_mongodb
```

## 🛠 Maintenance

### Sauvegardes

```bash
# Backup MySQL
docker exec hotel_transport_mysql mysqldump -u hotel_user -p hotel_transport > backup_$(date +%Y%m%d).sql

# Backup MongoDB
docker exec hotel_transport_mongodb mongodump --out=/backup
```

### Mises à Jour

```bash
# Arrêter les services
docker-compose down

# Mettre à jour le code
git pull

# Reconstruire les images si nécessaire
docker-compose build

# Relancer les services
docker-compose up -d

# Exécuter les migrations
docker exec -it hotel_transport_laravel php artisan migrate
```

## 📞 Support et Contact

Pour toute question ou problème:

- **Email**: support@hoteltransport.ma
- **Téléphone**: +212 5XX-XXXXXX
- **Documentation**: Cette documentation
- **Issues**: GitHub Issues

## 📄 Licence

Copyright © 2024 Hotel Transport Management. Tous droits réservés.

---

**Développé avec ❤️ au Maroc**

*Ce projet utilise: HTML, CSS, Bootstrap, JavaScript, React, Angular, Next.js, PHP, Laravel, Node.js, Express, MySQL, MongoDB, Docker, Nginx, Redis, N8N, JSON, API REST, WebSocket*

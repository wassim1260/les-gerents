# 🚗 Hotel Transport Management System

## Système de Gestion de Transport pour Groupe Hôtelier au Maroc

![Status](https://img.shields.io/badge/status-in_development-yellow)
![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-proprietary-red)

---

## 📋 Description

Système complet de gestion de transport pour groupes hôteliers au Maroc. Solution full-stack professionnelle avec 3 portails distincts (Client, Chauffeur, Admin) couvrant 10 villes marocaines.

### ✨ Caractéristiques Principales

- 🌍 **10 Villes Couvertes**: Casablanca, Rabat, Tanger, Marrakech, Agadir, Al Hoceima, Dakhla, Nador, Fès, Ouarzazate
- 🚗 **5 Types de Services**: Transfert Aéroport, Taxi, Van, Bus, Chauffeur Privé
- 💎 **4 Catégories de Luxe**: Standard, Confort, Luxe, VIP
- 👥 **3 Portails**: Client (React), Chauffeur (Angular), Admin (Next.js)
- 📱 **Suivi Temps Réel**: GPS tracking et notifications instantanées
- 💳 **Paiement Intégré**: Multiple méthodes de paiement
- 🤖 **Automatisation**: Workflows N8N pour notifications et gestion

---

## 🛠 Stack Technologique

### Frontend
- **HTML5, CSS3, Bootstrap 5** - Landing page et pages statiques
- **JavaScript ES6+** - Scripts et utilitaires
- **React 18+** - Portail Client (SPA moderne)
- **Angular 17+** - Portail Chauffeur (Mobile-first)
- **Next.js 14+** - Portail Admin (SSR, Dashboard)

### Backend
- **PHP 8.2+** - Scripts et APIs de compatibilité
- **Laravel 10+** - API REST principale
- **Node.js 20 + Express** - WebSocket et microservices

### Bases de Données
- **MySQL 8.0** - Données transactionnelles
- **MongoDB 7.0** - Logs et données temps réel
- **Redis 7** - Cache et sessions

### DevOps
- **Docker & Docker Compose** - Containerisation
- **Nginx** - Reverse proxy et load balancing
- **N8N** - Workflow automation

### APIs & Services
- **REST API** - Architecture RESTful
- **WebSocket** - Notifications temps réel
- **JSON** - Format d'échange
- **JWT** - Authentification

---

## 🚀 Démarrage Rapide

### Prérequis

- Docker Desktop installé
- 8 GB RAM minimum
- 20 GB espace disque

### Installation en 5 Minutes

```bash
# 1. Cloner le projet
git clone https://github.com/votre-org/hotel-transport-management.git
cd hotel-transport-management

# 2. Copier les variables d'environnement
cp .env.example .env

# 3. Démarrer tous les services
docker-compose up -d

# 4. Attendre 2-3 minutes que tous les services démarrent

# 5. Initialiser la base de données
docker exec -it hotel_transport_laravel php artisan migrate --seed

# 6. C'est prêt ! Ouvrir le navigateur
```

### URLs d'Accès

| Service | URL | Description |
|---------|-----|-------------|
| 🏠 Landing Page | http://localhost | Page d'accueil principale |
| 👤 Portail Client | http://localhost:3000 | Interface client (React) |
| 🚗 Portail Chauffeur | http://localhost:4200 | Interface chauffeur (Angular) |
| 👨‍💼 Portail Admin | http://localhost:3002 | Dashboard admin (Next.js) |
| 🔌 API Laravel | http://localhost:8000 | API REST principale |
| ⚡ API Node.js | http://localhost:3001 | API WebSocket |
| 🤖 N8N | http://localhost:5678 | Workflow automation |

### Compte Admin par Défaut

```
Email: admin@hoteltransport.ma
Mot de passe: Admin@123
```

---

## 📖 Documentation

- 📘 **[Guide d'Installation Complet](GUIDE_INSTALLATION.md)** - Installation détaillée pas à pas
- 📗 **[Documentation du Projet](PROJECT_DOCUMENTATION.md)** - Documentation technique complète
- 📙 **[Travail Réalisé](TRAVAIL_REALISE.md)** - Résumé des fonctionnalités implémentées
- 📕 **[Variables d'Environnement](.env.example)** - Configuration complète

---

## 🗂 Structure du Projet

```
hotel-transport-management/
│
├── 🐳 docker/              # Configuration Docker
│   ├── php/
│   ├── laravel/
│   ├── nodejs/
│   ├── react/
│   ├── angular/
│   ├── nextjs/
│   └── nginx/
│
├── ⚙️ backend/             # Applications Backend
│   ├── php/               # Scripts PHP & APIs
│   ├── laravel/           # Application Laravel
│   └── nodejs/            # Application Node.js
│
├── 🎨 frontend/            # Applications Frontend
│   ├── html/              # Landing page & pages statiques
│   ├── react-client/      # Portail Client (React)
│   ├── angular-driver/    # Portail Chauffeur (Angular)
│   └── nextjs-admin/      # Portail Admin (Next.js)
│
├── 💾 database/            # Scripts d'initialisation DB
│   ├── mysql/
│   └── mongodb/
│
├── 📄 docker-compose.yml   # Configuration Docker Compose
├── 📄 .env.example         # Variables d'environnement exemple
└── 📚 Documentation/       # Guides et docs
```

---

## 🌍 Villes Couvertes

| Ville | Nom Arabe | Code |
|-------|-----------|------|
| Casablanca | الدار البيضاء | CAS |
| Rabat | الرباط | RAB |
| Tanger | طنجة | TNG |
| Marrakech | مراكش | MAR |
| Agadir | أكادير | AGA |
| Al Hoceima | الحسيمة | AHU |
| Dakhla | الداخلة | DAK |
| Nador | الناظور | NDR |
| Fès | فاس | FES |
| Ouarzazate | ورزازات | OZZ |

---

## 🚗 Services de Transport

### 1. ✈️ Transfert Aéroport
- Navette depuis/vers tous les aéroports
- Suivi des vols en temps réel
- Réservation minimum 3h à l'avance

### 2. 🚕 Taxi
- Service standard 24/7
- Réservation instantanée
- Disponible dans toutes les villes

### 3. 🚐 Van
- Groupes jusqu'à 8 passagers
- Véhicules spacieux et confortables
- Idéal pour familles

### 4. 🚌 Bus
- Grands groupes jusqu'à 20 passagers
- Bus modernes et équipés
- Pour événements et excursions

### 5. ⭐ Chauffeur Privé
- Service VIP premium
- Véhicules de prestige
- Chauffeur professionnel

---

## 💎 Catégories de Luxe

| Catégorie | Multiplicateur | Caractéristiques |
|-----------|----------------|------------------|
| 🔹 Standard | x1.0 | Climatisation, Radio, Confort |
| 🔸 Confort | x1.3 | + WiFi, Eau, Chargeurs |
| 💠 Luxe | x1.8 | + Sièges cuir, Snacks, Premium |
| ⭐ VIP | x2.5 | + Champagne, Service exclusif |

---

## 📱 Fonctionnalités

### 👤 Portail Client
- ✅ Inscription et connexion
- ✅ Recherche et réservation de trajets
- ✅ Calcul automatique du prix
- 🔄 Suivi en temps réel avec GPS
- 🔄 Historique des réservations
- 🔄 Système de paiement
- 🔄 Évaluations et avis
- 🔄 Notifications temps réel

### 🚗 Portail Chauffeur
- ✅ Connexion sécurisée
- 🔄 Dashboard avec trajets assignés
- 🔄 Navigation GPS intégrée
- 🔄 Mise à jour statut en temps réel
- 🔄 Partage de position
- 🔄 Historique des courses
- 🔄 Statistiques et revenus

### 👨‍💼 Portail Admin
- ✅ Dashboard avec statistiques
- 🔄 Gestion des demandes
- 🔄 Affectation des chauffeurs
- 🔄 Gestion de la flotte
- 🔄 Inscription des chauffeurs
- 🔄 Suivi GPS complet
- 🔄 Analytics et rapports
- 🔄 Facturation

*Légende: ✅ Implémenté | 🔄 En développement*

---

## 🔐 Sécurité

- ✅ JWT Authentication
- ✅ Bcrypt password hashing
- ✅ SQL prepared statements (PDO)
- ✅ XSS & CSRF protection
- ✅ Input validation & sanitization
- ✅ Secure sessions (HttpOnly)
- ✅ CORS configuration
- 🔄 Rate limiting
- 🔄 2FA authentication

---

## 📊 APIs Disponibles

### API Laravel (Port 8000)
```
POST   /api/auth/register     - Inscription
POST   /api/auth/login        - Connexion
POST   /api/bookings          - Créer réservation
GET    /api/bookings          - Liste réservations
GET    /api/cities            - Liste villes
GET    /api/vehicles          - Liste véhicules
...et plus
```

### API Node.js (Port 3001)
```
WS     /socket.io             - WebSocket
POST   /api/tracking/update   - Mise à jour GPS
GET    /api/notifications     - Notifications
...et plus
```

### API PHP (Port 8080)
```
POST   /api/auth.php?action=register
POST   /api/bookings.php?action=create
GET    /api/bookings.php?action=list
...et plus
```

---

## 🧪 Tests

```bash
# Tests Laravel
docker exec -it hotel_transport_laravel php artisan test

# Tests Node.js
docker exec -it hotel_transport_nodejs npm test

# Tests React
docker exec -it hotel_transport_react npm test
```

---

## 🐛 Dépannage

### Problème: Containers ne démarrent pas
```bash
docker-compose down -v
docker system prune -a
docker-compose up -d --build
```

### Problème: Port déjà utilisé
```bash
# Modifier les ports dans docker-compose.yml
# Ou arrêter le service qui utilise le port
```

### Problème: Base de données non initialisée
```bash
docker exec -it hotel_transport_mysql mysql -u root -p
# Puis exécuter manuellement le script init.sql
```

**Plus de solutions** dans le [Guide d'Installation](GUIDE_INSTALLATION.md)

---

## 📈 Roadmap

### ✅ Phase 1 - Fondations (Session 1) - TERMINÉ
- Architecture Docker complète
- Base de données MySQL
- Landing page HTML/CSS/Bootstrap
- APIs PHP (Auth + Bookings)
- Documentation complète

### 🔄 Phase 2 - Backend (Sessions 2-3)
- Backend Laravel complet
- Backend Node.js + WebSocket
- MongoDB configuration
- API REST complète

### 🔄 Phase 3 - Frontend (Sessions 4-6)
- Portail Client React
- Portail Chauffeur Angular
- Portail Admin Next.js

### 🔄 Phase 4 - Finalisation (Sessions 7-n)
- Intégration N8N
- Tests complets
- CI/CD
- Déploiement production

---

## 🤝 Contribution

Ce projet est développé pour un groupe hôtelier spécifique.

---

## 📞 Support

- **Email**: contact@hoteltransport.ma
- **Téléphone**: +212 5XX-XXXXXX
- **Documentation**: Voir les fichiers MD dans le repo

---

## 📝 License

Copyright © 2024 Hotel Transport Management. Tous droits réservés.

---

## 🙏 Remerciements

Développé avec les technologies modernes pour offrir la meilleure expérience de transport au Maroc.

### Technologies Utilisées

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![React](https://img.shields.io/badge/React-61DAFB?style=for-the-badge&logo=react&logoColor=black)
![Angular](https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white)
![Next.js](https://img.shields.io/badge/Next.js-000000?style=for-the-badge&logo=next.js&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=node.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![MongoDB](https://img.shields.io/badge/MongoDB-47A248?style=for-the-badge&logo=mongodb&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Nginx](https://img.shields.io/badge/Nginx-009639?style=for-the-badge&logo=nginx&logoColor=white)

---

**Développé avec ❤️ au Maroc 🇲🇦**

*Pour toute question, consultez la documentation complète ou contactez le support.*

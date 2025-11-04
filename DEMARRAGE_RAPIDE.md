# 🚀 Démarrage Rapide - Hotel Transport Management

## Installation en 3 Commandes

```bash
# 1. Copier la configuration
cp .env.example .env

# 2. Démarrer avec le script automatique
./start.sh

# 3. Ouvrir le navigateur
# http://localhost
```

C'est tout ! Le script s'occupe de tout. ✨

---

## 🎯 Accès Rapide

### Interfaces Web

| 🌐 Service | 🔗 URL | 👤 Accès |
|-----------|--------|----------|
| Landing Page | http://localhost | Public |
| Portail Client | http://localhost:3000 | Inscription |
| Portail Chauffeur | http://localhost:4200 | Admin uniquement |
| Portail Admin | http://localhost:3002 | admin@hoteltransport.ma / Admin@123 |

### APIs

| 🔌 API | 🔗 URL | 📝 Usage |
|--------|--------|----------|
| Laravel | http://localhost:8000/api | API REST principale |
| Node.js | http://localhost:3001/api | WebSocket & temps réel |
| PHP | http://localhost:8080/api | Scripts de compatibilité |

### Outils

| 🛠 Outil | 🔗 URL | 🔑 Accès |
|---------|--------|----------|
| N8N | http://localhost:5678 | admin / admin123 |

---

## 📱 Tester le Système

### 1. Créer un Compte Client

```
1. Aller sur http://localhost
2. Cliquer sur "Inscription"
3. Remplir le formulaire
4. Vous recevrez un compte client
```

### 2. Faire une Réservation

```
1. Se connecter avec votre compte
2. Sélectionner ville de départ/arrivée
3. Choisir date et heure
4. Sélectionner type de véhicule
5. Voir le prix calculé automatiquement
6. Confirmer la réservation
```

### 3. Accéder au Dashboard Admin

```
1. Aller sur http://localhost:3002
2. Email: admin@hoteltransport.ma
3. Password: Admin@123
4. Voir toutes les réservations
5. Affecter des chauffeurs
```

---

## 🎮 Commandes Utiles

### Docker

```bash
# Voir les services
docker-compose ps

# Voir les logs
docker-compose logs -f

# Logs d'un service spécifique
docker-compose logs -f laravel
docker-compose logs -f nodejs

# Redémarrer un service
docker-compose restart laravel

# Arrêter tous les services
docker-compose down

# Tout supprimer et recommencer
docker-compose down -v
docker-compose up -d --build
```

### Laravel

```bash
# Entrer dans le container
docker exec -it hotel_transport_laravel bash

# Migrations
php artisan migrate

# Seeds
php artisan db:seed

# Cache
php artisan cache:clear
php artisan config:clear

# Voir les routes
php artisan route:list
```

### Base de Données

```bash
# MySQL
docker exec -it hotel_transport_mysql mysql -u hotel_user -p hotel_transport

# MongoDB
docker exec -it hotel_transport_mongodb mongo -u mongo_admin -p
```

---

## 🐛 Dépannage Rapide

### Problème: Ports occupés

```bash
# Voir qui utilise le port 80
sudo lsof -i :80

# Tuer le processus
sudo kill -9 <PID>

# Ou modifier les ports dans docker-compose.yml
```

### Problème: Containers ne démarrent pas

```bash
# Nettoyer et recommencer
docker-compose down -v
docker system prune -a
docker-compose up -d --build
```

### Problème: Erreur 502 Bad Gateway

```bash
# Vérifier que les backends sont actifs
docker-compose ps

# Redémarrer Nginx
docker-compose restart nginx
```

### Problème: Base de données vide

```bash
# Réinitialiser la base
docker exec -it hotel_transport_laravel php artisan migrate:fresh --seed
```

---

## 📊 Données de Test

### Compte Admin
```
Email: admin@hoteltransport.ma
Password: Admin@123
Role: Administrateur
```

### Villes Disponibles
```
✅ Casablanca (CAS)
✅ Rabat (RAB)
✅ Tanger (TNG)
✅ Marrakech (MAR)
✅ Agadir (AGA)
✅ Al Hoceima (AHU)
✅ Dakhla (DAK)
✅ Nador (NDR)
✅ Fès (FES)
✅ Ouarzazate (OZZ)
```

### Types de Véhicules
```
🚕 Taxi - 4 places - À partir de 50 MAD
🚐 Van - 8 places - À partir de 100 MAD
🚌 Bus - 20 places - À partir de 300 MAD
⭐ Chauffeur Privé - 4 places - À partir de 200 MAD
```

### Catégories de Luxe
```
🔹 Standard (x1.0) - Confort de base
🔸 Confort (x1.3) - WiFi + Eau
💠 Luxe (x1.8) - Cuir + Snacks
⭐ VIP (x2.5) - Service premium complet
```

---

## 🎯 Scénarios de Test

### Scénario 1: Réservation Simple

```
1. Client s'inscrit
2. Demande trajet Casablanca → Rabat
3. Sélectionne Taxi Standard
4. Prix calculé: ~300 MAD (88 km × 5 MAD/km + 50 base)
5. Réservation créée avec statut "pending"
6. Admin reçoit notification
7. Admin affecte un chauffeur
8. Client reçoit confirmation
```

### Scénario 2: Transfert Aéroport

```
1. Client demande transfert aéroport
2. Entre numéro de vol: AT123
3. Système suit le vol en temps réel
4. Chauffeur affecté 3h avant
5. Client reçoit SMS avec détails chauffeur
6. Chauffeur se met en route
7. Client suit position en temps réel
8. Trajet terminé, demande d'évaluation
```

### Scénario 3: Groupe (Van)

```
1. Client demande Van pour 8 personnes
2. Trajet Marrakech → Essaouira
3. Catégorie Confort (WiFi inclus)
4. Prix: ~1,500 MAD
5. Réservation 24h à l'avance
6. Admin affecte van disponible
7. Chauffeur accepte
8. Trajet avec arrêts prévus
```

---

## 📖 Documentation Complète

- 📘 [Guide d'Installation](GUIDE_INSTALLATION.md)
- 📗 [Documentation Technique](PROJECT_DOCUMENTATION.md)
- 📙 [Travail Réalisé](TRAVAIL_REALISE.md)
- 📕 [Configuration](. env.example)

---

## 💡 Conseils

### Pour le Développement

1. **Hot Reload** - Les frontends React/Angular/Next.js se rechargent automatiquement
2. **Logs** - Toujours avoir `docker-compose logs -f` ouvert dans un terminal
3. **Base de données** - Utiliser un client MySQL (TablePlus, DBeaver, etc.)
4. **APIs** - Tester avec Postman ou Insomnia

### Pour la Production

1. **Variables d'environnement** - Modifier `.env` avec valeurs production
2. **APP_DEBUG** - Mettre à `false`
3. **Secrets** - Générer de nouveaux secrets (JWT_SECRET, etc.)
4. **SSL** - Configurer certificats SSL/TLS
5. **Backups** - Mettre en place backups automatiques

---

## 🆘 Support

### Documentation
- README.md - Vue d'ensemble
- GUIDE_INSTALLATION.md - Installation détaillée
- PROJECT_DOCUMENTATION.md - Documentation complète

### Contact
- Email: contact@hoteltransport.ma
- Téléphone: +212 5XX-XXXXXX

---

## 🎉 C'est Parti !

Vous êtes prêt à utiliser le système. Commencez par :

```bash
# 1. Démarrer le système
./start.sh

# 2. Ouvrir le navigateur
open http://localhost

# 3. Créer un compte et tester !
```

**Bon développement ! 🚀**

---

*Développé avec ❤️ pour les groupes hôteliers au Maroc 🇲🇦*

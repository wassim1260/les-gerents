#!/bin/bash

# ============================================
# Hotel Transport Management System
# Script de Démarrage Rapide
# ============================================

set -e  # Arrêter en cas d'erreur

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Fonctions d'affichage
print_header() {
    echo ""
    echo -e "${PURPLE}╔════════════════════════════════════════════════╗${NC}"
    echo -e "${PURPLE}║                                                ║${NC}"
    echo -e "${PURPLE}║   🚗 HOTEL TRANSPORT MANAGEMENT SYSTEM 🚗     ║${NC}"
    echo -e "${PURPLE}║                                                ║${NC}"
    echo -e "${PURPLE}╚════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_step() {
    echo -e "${CYAN}▶ $1${NC}"
}

# Vérifier Docker
check_docker() {
    print_step "Vérification de Docker..."
    if ! command -v docker &> /dev/null; then
        print_error "Docker n'est pas installé."
        print_info "Installez Docker Desktop depuis: https://www.docker.com/products/docker-desktop"
        exit 1
    fi
    print_success "Docker trouvé: $(docker --version)"
}

# Vérifier Docker Compose
check_docker_compose() {
    print_step "Vérification de Docker Compose..."
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose n'est pas installé."
        exit 1
    fi
    print_success "Docker Compose trouvé: $(docker-compose --version)"
}

# Vérifier les ports disponibles
check_ports() {
    print_step "Vérification des ports..."

    PORTS=(80 3000 3001 3002 4200 5678 8000 8080)
    PORTS_BUSY=()

    for port in "${PORTS[@]}"; do
        if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null 2>&1 ; then
            PORTS_BUSY+=($port)
        fi
    done

    if [ ${#PORTS_BUSY[@]} -gt 0 ]; then
        print_warning "Ports occupés: ${PORTS_BUSY[*]}"
        print_info "Arrêtez les services utilisant ces ports ou modifiez docker-compose.yml"
        read -p "Voulez-vous continuer quand même? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    else
        print_success "Tous les ports sont disponibles"
    fi
}

# Créer .env si nécessaire
setup_env() {
    print_step "Configuration de l'environnement..."

    if [ ! -f .env ]; then
        print_info "Copie de .env.example vers .env"
        cp .env.example .env
        print_success ".env créé avec succès"
    else
        print_success ".env existe déjà"
    fi
}

# Lancer Docker Compose
start_docker() {
    print_step "Démarrage des services Docker..."
    print_info "Cela peut prendre 2-3 minutes..."

    docker-compose up -d

    print_success "Services Docker démarrés"
}

# Attendre que MySQL soit prêt
wait_for_mysql() {
    print_step "Attente de MySQL..."

    max_attempts=30
    attempt=0

    while [ $attempt -lt $max_attempts ]; do
        if docker exec hotel_transport_mysql mysqladmin ping -h localhost --silent 2>/dev/null; then
            print_success "MySQL est prêt"
            return 0
        fi

        attempt=$((attempt + 1))
        echo -n "."
        sleep 2
    done

    print_error "MySQL n'a pas démarré dans le temps imparti"
    return 1
}

# Initialiser la base de données
init_database() {
    print_step "Initialisation de la base de données..."

    # Attendre MySQL
    if wait_for_mysql; then
        print_info "Exécution des migrations (si Laravel est configuré)..."

        if docker exec hotel_transport_laravel test -f artisan 2>/dev/null; then
            docker exec hotel_transport_laravel php artisan migrate --force 2>/dev/null || true
            docker exec hotel_transport_laravel php artisan db:seed --force 2>/dev/null || true
            print_success "Base de données initialisée"
        else
            print_warning "Laravel non configuré, migrations ignorées"
        fi
    else
        print_warning "Impossible d'initialiser la base de données"
    fi
}

# Afficher les URLs
show_urls() {
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║          🎉 Installation Terminée! 🎉         ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${CYAN}📍 URLs d'accès:${NC}"
    echo ""
    echo -e "  ${BLUE}🏠 Landing Page:${NC}      http://localhost"
    echo -e "  ${BLUE}👤 Portail Client:${NC}    http://localhost:3000"
    echo -e "  ${BLUE}🚗 Portail Chauffeur:${NC} http://localhost:4200"
    echo -e "  ${BLUE}👨‍💼 Portail Admin:${NC}     http://localhost:3002"
    echo ""
    echo -e "  ${BLUE}🔌 API Laravel:${NC}       http://localhost:8000/api"
    echo -e "  ${BLUE}⚡ API Node.js:${NC}       http://localhost:3001/api"
    echo -e "  ${BLUE}📜 API PHP:${NC}           http://localhost:8080/api"
    echo ""
    echo -e "  ${BLUE}🤖 N8N Workflows:${NC}    http://localhost:5678"
    echo ""
    echo -e "${CYAN}🔐 Compte admin par défaut:${NC}"
    echo -e "  Email:    ${GREEN}admin@hoteltransport.ma${NC}"
    echo -e "  Password: ${GREEN}Admin@123${NC}"
    echo ""
    echo -e "${CYAN}📊 Vérifier le statut des services:${NC}"
    echo -e "  ${YELLOW}docker-compose ps${NC}"
    echo ""
    echo -e "${CYAN}📝 Voir les logs:${NC}"
    echo -e "  ${YELLOW}docker-compose logs -f${NC}"
    echo ""
    echo -e "${CYAN}🛑 Arrêter les services:${NC}"
    echo -e "  ${YELLOW}docker-compose down${NC}"
    echo ""
}

# Afficher le statut des services
show_status() {
    echo ""
    print_step "Statut des services:"
    echo ""
    docker-compose ps
    echo ""
}

# Menu principal
show_menu() {
    clear
    print_header
    echo -e "${CYAN}Que voulez-vous faire?${NC}"
    echo ""
    echo "  1) 🚀 Démarrer tous les services"
    echo "  2) 🛑 Arrêter tous les services"
    echo "  3) 🔄 Redémarrer tous les services"
    echo "  4) 📊 Voir le statut des services"
    echo "  5) 📝 Voir les logs"
    echo "  6) 🗄️  Réinitialiser la base de données"
    echo "  7) 🧹 Nettoyer tout (attention!)"
    echo "  8) ❌ Quitter"
    echo ""
}

# Fonction principale
main() {
    print_header

    # Vérifications préliminaires
    check_docker
    check_docker_compose
    check_ports

    # Setup
    setup_env

    # Démarrage
    start_docker

    # Initialisation
    init_database

    # Affichage
    show_status
    show_urls
}

# Menu interactif
interactive_menu() {
    while true; do
        show_menu
        read -p "Votre choix: " choice

        case $choice in
            1)
                print_header
                main
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            2)
                print_header
                print_step "Arrêt des services..."
                docker-compose down
                print_success "Services arrêtés"
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            3)
                print_header
                print_step "Redémarrage des services..."
                docker-compose restart
                print_success "Services redémarrés"
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            4)
                print_header
                show_status
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            5)
                print_header
                print_info "Appuyez sur Ctrl+C pour quitter les logs"
                docker-compose logs -f
                ;;
            6)
                print_header
                print_warning "Cela va supprimer toutes les données de la base!"
                read -p "Êtes-vous sûr? (yes/no): " confirm
                if [ "$confirm" = "yes" ]; then
                    docker exec hotel_transport_laravel php artisan migrate:fresh --seed --force 2>/dev/null || true
                    print_success "Base de données réinitialisée"
                fi
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            7)
                print_header
                print_error "ATTENTION: Cela va tout supprimer (containers, volumes, images)"
                read -p "Êtes-vous VRAIMENT sûr? (yes/no): " confirm
                if [ "$confirm" = "yes" ]; then
                    docker-compose down -v
                    docker system prune -a -f
                    print_success "Nettoyage complet effectué"
                fi
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            8)
                print_header
                print_info "Au revoir!"
                exit 0
                ;;
            *)
                print_error "Choix invalide"
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
        esac
    done
}

# Point d'entrée
if [ "$1" = "--menu" ] || [ "$1" = "-m" ]; then
    interactive_menu
else
    main
fi

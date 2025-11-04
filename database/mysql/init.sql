-- Hotel Transport Management System Database Schema
-- Created for a full-stack transport management platform

CREATE DATABASE IF NOT EXISTS hotel_transport;
USE hotel_transport;

-- ============================================
-- TABLES DE BASE
-- ============================================

-- Table des villes
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    name_ar VARCHAR(100),
    code VARCHAR(10) NOT NULL UNIQUE,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des villes marocaines
INSERT INTO cities (name, name_ar, code, latitude, longitude) VALUES
('Casablanca', 'الدار البيضاء', 'CAS', 33.5731, -7.5898),
('Rabat', 'الرباط', 'RAB', 34.0209, -6.8416),
('Tanger', 'طنجة', 'TNG', 35.7595, -5.8340),
('Marrakech', 'مراكش', 'MAR', 31.6295, -7.9811),
('Agadir', 'أكادير', 'AGA', 30.4278, -9.5981),
('Al Hoceima', 'الحسيمة', 'AHU', 35.2517, -3.9317),
('Dakhla', 'الداخلة', 'DAK', 23.7154, -15.9582),
('Nador', 'الناظور', 'NDR', 35.1681, -2.9333),
('Fès', 'فاس', 'FES', 34.0181, -5.0078),
('Ouarzazate', 'ورزازات', 'OZZ', 30.9335, -6.9370);

-- Table des rôles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name, description, permissions) VALUES
('admin', 'Administrateur système', '["all"]'),
('driver', 'Chauffeur', '["view_trips", "update_trip_status", "view_profile"]'),
('client', 'Client', '["create_booking", "view_bookings", "cancel_booking"]');

-- Table des utilisateurs (centralisée)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    phone_verified BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des profils clients
CREATE TABLE client_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    company_name VARCHAR(255),
    address TEXT,
    city_id INT,
    postal_code VARCHAR(20),
    loyalty_points INT DEFAULT 0,
    preferred_payment_method ENUM('cash', 'card', 'transfer', 'wallet') DEFAULT 'cash',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des types de véhicules
CREATE TABLE vehicle_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100),
    description TEXT,
    capacity INT NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    price_per_km DECIMAL(10, 2) NOT NULL,
    icon VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des types de véhicules
INSERT INTO vehicle_types (name, name_ar, description, capacity, base_price, price_per_km) VALUES
('Taxi', 'سيارة أجرة', 'Voiture de tourisme standard', 4, 50.00, 5.00),
('Van', 'فان', 'Véhicule spacieux pour groupes', 8, 100.00, 7.00),
('Bus', 'حافلة', 'Transport de groupe', 20, 300.00, 10.00),
('Chauffeur Privé', 'سائق خاص', 'Service de chauffeur privé haut de gamme', 4, 200.00, 15.00);

-- Table des catégories de luxe
CREATE TABLE luxury_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100),
    description TEXT,
    price_multiplier DECIMAL(5, 2) DEFAULT 1.00,
    features JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO luxury_categories (name, name_ar, description, price_multiplier, features) VALUES
('Standard', 'عادي', 'Véhicule confortable et fiable', 1.00, '["climatisation", "radio"]'),
('Confort', 'مريح', 'Véhicule avec équipements supplémentaires', 1.30, '["climatisation", "wifi", "eau"]'),
('Luxe', 'فخم', 'Véhicule premium avec services haut de gamme', 1.80, '["climatisation", "wifi", "eau", "snacks", "cuir"]'),
('VIP', 'في آي بي', 'Service exclusif avec véhicules de prestige', 2.50, '["climatisation", "wifi", "eau", "snacks", "cuir", "champagne", "chauffeur_costume"]');

-- Table des véhicules
CREATE TABLE vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_type_id INT NOT NULL,
    luxury_category_id INT NOT NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(50),
    license_plate VARCHAR(50) NOT NULL UNIQUE,
    vin VARCHAR(100),
    capacity INT NOT NULL,
    fuel_type ENUM('essence', 'diesel', 'electric', 'hybrid') DEFAULT 'essence',
    mileage INT DEFAULT 0,
    status ENUM('available', 'in_use', 'maintenance', 'inactive') DEFAULT 'available',
    insurance_expiry DATE,
    technical_control_expiry DATE,
    photos JSON,
    features JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (luxury_category_id) REFERENCES luxury_categories(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_vehicle_type (vehicle_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des profils chauffeurs
CREATE TABLE driver_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    license_number VARCHAR(100) NOT NULL UNIQUE,
    license_expiry DATE NOT NULL,
    license_category VARCHAR(50),
    experience_years INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 5.00,
    total_trips INT DEFAULT 0,
    status ENUM('available', 'on_trip', 'offline', 'suspended') DEFAULT 'offline',
    current_city_id INT,
    current_latitude DECIMAL(10, 8),
    current_longitude DECIMAL(11, 8),
    vehicle_id INT,
    languages JSON,
    certifications JSON,
    background_check_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (current_city_id) REFERENCES cities(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des types de services
CREATE TABLE service_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100),
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    requires_flight_info BOOLEAN DEFAULT FALSE,
    requires_return_trip BOOLEAN DEFAULT FALSE,
    advance_booking_hours INT DEFAULT 2,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO service_types (name, name_ar, slug, description, requires_flight_info, advance_booking_hours) VALUES
('Transfert Aéroport', 'نقل المطار', 'airport-transfer', 'Service de transport depuis/vers l\'aéroport', TRUE, 3),
('Taxi', 'سيارة أجرة', 'taxi', 'Service de taxi standard dans la ville', FALSE, 1),
('Van', 'فان', 'van', 'Transport de groupe avec van spacieux', FALSE, 2),
('Bus', 'حافلة', 'bus', 'Transport de grands groupes', FALSE, 6),
('Chauffeur Privé', 'سائق خاص', 'private-driver', 'Service de chauffeur privé personnalisé', FALSE, 4);

-- Table des réservations (bookings)
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_number VARCHAR(50) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    service_type_id INT NOT NULL,
    vehicle_type_id INT NOT NULL,
    luxury_category_id INT NOT NULL,

    -- Détails du trajet
    pickup_address TEXT NOT NULL,
    pickup_city_id INT NOT NULL,
    pickup_latitude DECIMAL(10, 8),
    pickup_longitude DECIMAL(11, 8),
    pickup_datetime DATETIME NOT NULL,

    dropoff_address TEXT NOT NULL,
    dropoff_city_id INT NOT NULL,
    dropoff_latitude DECIMAL(10, 8),
    dropoff_longitude DECIMAL(11, 8),

    -- Information vol (optionnel)
    flight_number VARCHAR(50),
    flight_arrival_time DATETIME,

    -- Passagers
    passenger_count INT DEFAULT 1,
    passenger_names JSON,

    -- Prix
    distance_km DECIMAL(10, 2),
    estimated_duration_minutes INT,
    base_price DECIMAL(10, 2) NOT NULL,
    distance_price DECIMAL(10, 2) DEFAULT 0,
    luxury_surcharge DECIMAL(10, 2) DEFAULT 0,
    service_fee DECIMAL(10, 2) DEFAULT 0,
    total_price DECIMAL(10, 2) NOT NULL,

    -- Statut
    status ENUM(
        'pending',
        'confirmed',
        'assigned',
        'driver_en_route',
        'driver_arrived',
        'in_progress',
        'completed',
        'cancelled',
        'no_show'
    ) DEFAULT 'pending',

    -- Affectation
    assigned_driver_id INT,
    assigned_vehicle_id INT,
    assigned_at TIMESTAMP NULL,

    -- Notes et préférences
    special_requests TEXT,
    admin_notes TEXT,
    cancellation_reason TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (luxury_category_id) REFERENCES luxury_categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (pickup_city_id) REFERENCES cities(id) ON DELETE RESTRICT,
    FOREIGN KEY (dropoff_city_id) REFERENCES cities(id) ON DELETE RESTRICT,
    FOREIGN KEY (assigned_driver_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,

    INDEX idx_booking_number (booking_number),
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    INDEX idx_pickup_datetime (pickup_datetime),
    INDEX idx_assigned_driver (assigned_driver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des statuts de trajet (historique)
CREATE TABLE booking_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de suivi GPS en temps réel
CREATE TABLE trip_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    driver_id INT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    speed DECIMAL(5, 2),
    heading INT,
    accuracy DECIMAL(6, 2),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des paiements
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer', 'wallet', 'online') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    payment_gateway VARCHAR(100),
    payment_date TIMESTAMP NULL,
    refund_amount DECIMAL(10, 2),
    refund_date TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT,
    INDEX idx_booking (booking_id),
    INDEX idx_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des évaluations
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL UNIQUE,
    client_id INT NOT NULL,
    driver_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    vehicle_rating INT CHECK (vehicle_rating BETWEEN 1 AND 5),
    punctuality_rating INT CHECK (punctuality_rating BETWEEN 1 AND 5),
    service_rating INT CHECK (service_rating BETWEEN 1 AND 5),
    comment TEXT,
    response TEXT,
    responded_by INT,
    responded_at TIMESTAMP NULL,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (responded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_driver (driver_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des notifications
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des paramètres système
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paramètres par défaut
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('company_name', 'Hotel Transport Management', 'string', 'Nom de l\'entreprise', TRUE),
('company_email', 'contact@hoteltransport.ma', 'string', 'Email de contact', TRUE),
('company_phone', '+212 5XX-XXXXXX', 'string', 'Téléphone de contact', TRUE),
('booking_advance_hours', '2', 'number', 'Heures minimum avant réservation', TRUE),
('cancellation_hours', '6', 'number', 'Heures avant pour annulation gratuite', TRUE),
('service_fee_percentage', '10', 'number', 'Pourcentage de frais de service', FALSE),
('vat_percentage', '20', 'number', 'Pourcentage TVA', FALSE),
('currency', 'MAD', 'string', 'Devise', TRUE),
('max_booking_days', '90', 'number', 'Nombre maximum de jours pour réservation future', TRUE),
('enable_sms_notifications', 'true', 'boolean', 'Activer notifications SMS', FALSE),
('enable_email_notifications', 'true', 'boolean', 'Activer notifications email', FALSE);

-- Table des logs d'audit
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tokens de réinitialisation de mot de passe
CREATE TABLE password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des sessions
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(500) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES DE TEST
-- ============================================

-- Admin par défaut (password: Admin@123)
INSERT INTO users (role_id, email, password, first_name, last_name, phone, email_verified) VALUES
(1, 'admin@hoteltransport.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', '+212600000000', TRUE);

-- Quelques véhicules de test
INSERT INTO vehicles (vehicle_type_id, luxury_category_id, brand, model, year, color, license_plate, capacity, status) VALUES
(1, 1, 'Dacia', 'Logan', 2022, 'Blanc', 'A-12345', 4, 'available'),
(1, 2, 'Renault', 'Symbol', 2023, 'Noir', 'B-67890', 4, 'available'),
(2, 2, 'Hyundai', 'H1', 2023, 'Gris', 'C-11111', 8, 'available'),
(3, 1, 'Mercedes', 'Sprinter', 2022, 'Blanc', 'D-22222', 20, 'available'),
(4, 4, 'Mercedes', 'Classe S', 2024, 'Noir', 'E-99999', 4, 'available');

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue des réservations avec détails
CREATE VIEW v_bookings_detailed AS
SELECT
    b.id,
    b.booking_number,
    b.status,
    b.pickup_datetime,

    c.id as client_id,
    CONCAT(c.first_name, ' ', c.last_name) as client_name,
    c.email as client_email,
    c.phone as client_phone,

    d.id as driver_id,
    CONCAT(d.first_name, ' ', d.last_name) as driver_name,
    d.phone as driver_phone,
    dp.rating as driver_rating,

    st.name as service_type,
    vt.name as vehicle_type,
    lc.name as luxury_category,

    pc.name as pickup_city,
    dc.name as dropoff_city,

    v.brand as vehicle_brand,
    v.model as vehicle_model,
    v.license_plate,

    b.total_price,
    b.distance_km,

    b.created_at,
    b.updated_at
FROM bookings b
JOIN users c ON b.client_id = c.id
LEFT JOIN users d ON b.assigned_driver_id = d.id
LEFT JOIN driver_profiles dp ON d.id = dp.user_id
JOIN service_types st ON b.service_type_id = st.id
JOIN vehicle_types vt ON b.vehicle_type_id = vt.id
JOIN luxury_categories lc ON b.luxury_category_id = lc.id
JOIN cities pc ON b.pickup_city_id = pc.id
JOIN cities dc ON b.dropoff_city_id = dc.id
LEFT JOIN vehicles v ON b.assigned_vehicle_id = v.id;

-- Vue des statistiques des chauffeurs
CREATE VIEW v_driver_statistics AS
SELECT
    u.id,
    CONCAT(u.first_name, ' ', u.last_name) as driver_name,
    dp.rating,
    dp.total_trips,
    dp.status,
    COUNT(DISTINCT b.id) as completed_trips,
    AVG(r.rating) as avg_review_rating,
    SUM(CASE WHEN b.status = 'completed' THEN b.total_price ELSE 0 END) as total_earnings
FROM users u
JOIN driver_profiles dp ON u.id = dp.user_id
LEFT JOIN bookings b ON u.id = b.assigned_driver_id
LEFT JOIN reviews r ON u.id = r.driver_id
WHERE u.role_id = 2
GROUP BY u.id, u.first_name, u.last_name, dp.rating, dp.total_trips, dp.status;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger pour générer un numéro de réservation unique
DELIMITER //
CREATE TRIGGER before_booking_insert
BEFORE INSERT ON bookings
FOR EACH ROW
BEGIN
    IF NEW.booking_number IS NULL OR NEW.booking_number = '' THEN
        SET NEW.booking_number = CONCAT('BK', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
    END IF;
END;//
DELIMITER ;

-- Trigger pour enregistrer l'historique des statuts
DELIMITER //
CREATE TRIGGER after_booking_status_update
AFTER UPDATE ON bookings
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO booking_status_history (booking_id, old_status, new_status)
        VALUES (NEW.id, OLD.status, NEW.status);
    END IF;
END;//
DELIMITER ;

-- Trigger pour mettre à jour le rating du chauffeur
DELIMITER //
CREATE TRIGGER after_review_insert
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE driver_profiles dp
    SET dp.rating = (
        SELECT AVG(r.rating)
        FROM reviews r
        WHERE r.driver_id = NEW.driver_id
    )
    WHERE dp.user_id = NEW.driver_id;
END;//
DELIMITER ;

-- ============================================
-- PROCÉDURES STOCKÉES
-- ============================================

-- Procédure pour obtenir les chauffeurs disponibles
DELIMITER //
CREATE PROCEDURE get_available_drivers(
    IN p_city_id INT,
    IN p_vehicle_type_id INT,
    IN p_datetime DATETIME
)
BEGIN
    SELECT
        u.id,
        CONCAT(u.first_name, ' ', u.last_name) as name,
        u.phone,
        dp.rating,
        dp.total_trips,
        dp.current_latitude,
        dp.current_longitude,
        v.brand,
        v.model,
        v.license_plate
    FROM users u
    JOIN driver_profiles dp ON u.id = dp.user_id
    JOIN vehicles v ON dp.vehicle_id = v.id
    WHERE dp.status = 'available'
        AND v.vehicle_type_id = p_vehicle_type_id
        AND v.status = 'available'
        AND (dp.current_city_id = p_city_id OR dp.current_city_id IS NULL)
        AND NOT EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.assigned_driver_id = u.id
                AND b.pickup_datetime BETWEEN p_datetime - INTERVAL 2 HOUR AND p_datetime + INTERVAL 2 HOUR
                AND b.status IN ('confirmed', 'assigned', 'driver_en_route', 'in_progress')
        )
    ORDER BY dp.rating DESC, dp.total_trips DESC
    LIMIT 10;
END;//
DELIMITER ;

-- Procédure pour calculer le prix d'une réservation
DELIMITER //
CREATE PROCEDURE calculate_booking_price(
    IN p_vehicle_type_id INT,
    IN p_luxury_category_id INT,
    IN p_distance_km DECIMAL(10,2),
    OUT p_base_price DECIMAL(10,2),
    OUT p_distance_price DECIMAL(10,2),
    OUT p_luxury_surcharge DECIMAL(10,2),
    OUT p_total_price DECIMAL(10,2)
)
BEGIN
    DECLARE v_base_price DECIMAL(10,2);
    DECLARE v_price_per_km DECIMAL(10,2);
    DECLARE v_luxury_multiplier DECIMAL(5,2);

    -- Obtenir le prix de base et le prix par km
    SELECT base_price, price_per_km
    INTO v_base_price, v_price_per_km
    FROM vehicle_types
    WHERE id = p_vehicle_type_id;

    -- Obtenir le multiplicateur de luxe
    SELECT price_multiplier
    INTO v_luxury_multiplier
    FROM luxury_categories
    WHERE id = p_luxury_category_id;

    -- Calculer les prix
    SET p_base_price = v_base_price;
    SET p_distance_price = v_price_per_km * p_distance_km;
    SET p_luxury_surcharge = (p_base_price + p_distance_price) * (v_luxury_multiplier - 1);
    SET p_total_price = p_base_price + p_distance_price + p_luxury_surcharge;
END;//
DELIMITER ;

DELIMITER ;

-- Index supplémentaires pour les performances
CREATE INDEX idx_bookings_dates ON bookings(pickup_datetime, status);
CREATE INDEX idx_drivers_location ON driver_profiles(current_city_id, status);
CREATE INDEX idx_vehicles_availability ON vehicles(vehicle_type_id, status);

-- ============================================
-- FIN DU SCRIPT
-- ============================================

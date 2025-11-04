<?php
/**
 * API de gestion des réservations
 * Permet aux clients de créer, consulter et gérer leurs réservations
 */

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Vérifier l'authentification pour certaines actions
$requiresAuth = ['create', 'list', 'get', 'update', 'cancel'];
if (in_array($action, $requiresAuth)) {
    $user = authenticateUser();
    if (!$user) {
        handleError('Authentification requise', 401);
    }
}

switch ($action) {
    case 'create':
        if ($method === 'POST') {
            handleCreateBooking($user);
        }
        break;

    case 'list':
        if ($method === 'GET') {
            handleListBookings($user);
        }
        break;

    case 'get':
        if ($method === 'GET') {
            handleGetBooking($user);
        }
        break;

    case 'update':
        if ($method === 'PUT') {
            handleUpdateBooking($user);
        }
        break;

    case 'cancel':
        if ($method === 'POST') {
            handleCancelBooking($user);
        }
        break;

    case 'calculate-price':
        if ($method === 'POST') {
            handleCalculatePrice();
        }
        break;

    case 'available-vehicles':
        if ($method === 'GET') {
            handleAvailableVehicles();
        }
        break;

    default:
        handleError('Action non reconnue', 404);
}

/**
 * Authentifier l'utilisateur via le token
 */
function authenticateUser() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (empty($authHeader)) {
        return false;
    }

    $token = str_replace('Bearer ', '', $authHeader);

    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare("
            SELECT s.user_id, u.*, r.name as role_name
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE s.token = ? AND s.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur d'authentification: " . $e->getMessage());
        return false;
    }
}

/**
 * Créer une nouvelle réservation
 */
function handleCreateBooking($user) {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validation des champs requis
    $required = [
        'service_type_id', 'vehicle_type_id', 'luxury_category_id',
        'pickup_address', 'pickup_city_id', 'pickup_datetime',
        'dropoff_address', 'dropoff_city_id', 'passenger_count'
    ];

    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            handleError("Le champ $field est requis");
        }
    }

    $pdo = getDbConnection();

    try {
        // Récupérer les coordonnées des villes
        $stmt = $pdo->prepare("SELECT latitude, longitude FROM cities WHERE id = ?");

        $stmt->execute([$data['pickup_city_id']]);
        $pickupCity = $stmt->fetch();

        $stmt->execute([$data['dropoff_city_id']]);
        $dropoffCity = $stmt->fetch();

        if (!$pickupCity || !$dropoffCity) {
            handleError('Ville invalide');
        }

        // Calculer la distance
        $distance = calculateDistance(
            $pickupCity['latitude'],
            $pickupCity['longitude'],
            $dropoffCity['latitude'],
            $dropoffCity['longitude']
        );

        // Calculer le prix
        $prices = calculateBookingPrice(
            $data['vehicle_type_id'],
            $data['luxury_category_id'],
            $distance
        );

        // Générer un numéro de réservation unique
        $bookingNumber = 'BK' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Préparer les données pour l'insertion
        $pickupDatetime = date('Y-m-d H:i:s', strtotime($data['pickup_datetime']));

        // Vérifier que la réservation est assez en avance
        $hoursDiff = (strtotime($pickupDatetime) - time()) / 3600;
        $minHours = 2; // Minimum 2 heures à l'avance

        $stmt = $pdo->prepare("SELECT advance_booking_hours FROM service_types WHERE id = ?");
        $stmt->execute([$data['service_type_id']]);
        $service = $stmt->fetch();
        if ($service) {
            $minHours = $service['advance_booking_hours'];
        }

        if ($hoursDiff < $minHours) {
            handleError("Vous devez réserver au moins $minHours heures à l'avance");
        }

        // Commencer une transaction
        $pdo->beginTransaction();

        // Insérer la réservation
        $stmt = $pdo->prepare("
            INSERT INTO bookings (
                booking_number, client_id, service_type_id, vehicle_type_id, luxury_category_id,
                pickup_address, pickup_city_id, pickup_latitude, pickup_longitude, pickup_datetime,
                dropoff_address, dropoff_city_id, dropoff_latitude, dropoff_longitude,
                passenger_count, passenger_names, flight_number, flight_arrival_time,
                distance_km, base_price, distance_price, luxury_surcharge, total_price,
                special_requests, status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending'
            )
        ");

        $stmt->execute([
            $bookingNumber,
            $user['id'],
            $data['service_type_id'],
            $data['vehicle_type_id'],
            $data['luxury_category_id'],
            $data['pickup_address'],
            $data['pickup_city_id'],
            $data['pickup_latitude'] ?? $pickupCity['latitude'],
            $data['pickup_longitude'] ?? $pickupCity['longitude'],
            $pickupDatetime,
            $data['dropoff_address'],
            $data['dropoff_city_id'],
            $data['dropoff_latitude'] ?? $dropoffCity['latitude'],
            $data['dropoff_longitude'] ?? $dropoffCity['longitude'],
            $data['passenger_count'],
            json_encode($data['passenger_names'] ?? []),
            $data['flight_number'] ?? null,
            $data['flight_arrival_time'] ?? null,
            $distance,
            $prices['base_price'],
            $prices['distance_price'],
            $prices['luxury_surcharge'],
            $prices['total_price'],
            $data['special_requests'] ?? null
        ]);

        $bookingId = $pdo->lastInsertId();

        // Logger l'activité
        logActivity($user['id'], 'create_booking', 'booking', $bookingId, [
            'booking_number' => $bookingNumber,
            'total_price' => $prices['total_price']
        ]);

        // Envoyer une notification à l'utilisateur
        sendNotification(
            $user['id'],
            'booking_created',
            'Réservation créée',
            "Votre réservation $bookingNumber a été créée avec succès",
            ['booking_id' => $bookingId, 'booking_number' => $bookingNumber]
        );

        // Envoyer un email de confirmation
        $emailBody = "
            <h2>Confirmation de réservation</h2>
            <p>Bonjour {$user['first_name']} {$user['last_name']},</p>
            <p>Votre réservation a été créée avec succès.</p>
            <p><strong>Numéro de réservation:</strong> $bookingNumber</p>
            <p><strong>Date et heure:</strong> " . date('d/m/Y H:i', strtotime($pickupDatetime)) . "</p>
            <p><strong>Départ:</strong> {$data['pickup_address']}</p>
            <p><strong>Arrivée:</strong> {$data['dropoff_address']}</p>
            <p><strong>Prix total:</strong> " . formatPrice($prices['total_price']) . "</p>
            <p>Nous vous informerons dès qu'un chauffeur sera affecté à votre trajet.</p>
        ";
        sendEmail($user['email'], 'Confirmation de réservation - HTM Transport', $emailBody);

        // Commit de la transaction
        $pdo->commit();

        // Récupérer la réservation complète
        $stmt = $pdo->prepare("
            SELECT b.*,
                   st.name as service_type_name,
                   vt.name as vehicle_type_name,
                   lc.name as luxury_category_name,
                   pc.name as pickup_city_name,
                   dc.name as dropoff_city_name
            FROM bookings b
            JOIN service_types st ON b.service_type_id = st.id
            JOIN vehicle_types vt ON b.vehicle_type_id = vt.id
            JOIN luxury_categories lc ON b.luxury_category_id = lc.id
            JOIN cities pc ON b.pickup_city_id = pc.id
            JOIN cities dc ON b.dropoff_city_id = dc.id
            WHERE b.id = ?
        ");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();

        handleSuccess($booking, 'Réservation créée avec succès');

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erreur lors de la création de la réservation: " . $e->getMessage());
        handleError('Erreur lors de la création de la réservation', 500);
    }
}

/**
 * Lister les réservations de l'utilisateur
 */
function handleListBookings($user) {
    $pdo = getDbConnection();

    $status = $_GET['status'] ?? null;
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 20;
    $offset = ($page - 1) * $limit;

    try {
        $whereClause = "WHERE b.client_id = ?";
        $params = [$user['id']];

        if ($status) {
            $whereClause .= " AND b.status = ?";
            $params[] = $status;
        }

        // Compter le total
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings b $whereClause");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Récupérer les réservations
        $sql = "
            SELECT b.*,
                   st.name as service_type_name,
                   vt.name as vehicle_type_name,
                   lc.name as luxury_category_name,
                   pc.name as pickup_city_name,
                   dc.name as dropoff_city_name,
                   d.first_name as driver_first_name,
                   d.last_name as driver_last_name,
                   d.phone as driver_phone,
                   v.brand as vehicle_brand,
                   v.model as vehicle_model,
                   v.license_plate as vehicle_plate
            FROM bookings b
            JOIN service_types st ON b.service_type_id = st.id
            JOIN vehicle_types vt ON b.vehicle_type_id = vt.id
            JOIN luxury_categories lc ON b.luxury_category_id = lc.id
            JOIN cities pc ON b.pickup_city_id = pc.id
            JOIN cities dc ON b.dropoff_city_id = dc.id
            LEFT JOIN users d ON b.assigned_driver_id = d.id
            LEFT JOIN vehicles v ON b.assigned_vehicle_id = v.id
            $whereClause
            ORDER BY b.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        handleSuccess([
            'bookings' => $bookings,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$limit,
                'total' => (int)$total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);

    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des réservations: " . $e->getMessage());
        handleError('Erreur lors de la récupération des réservations', 500);
    }
}

/**
 * Récupérer une réservation spécifique
 */
function handleGetBooking($user) {
    $bookingId = $_GET['id'] ?? null;

    if (!$bookingId) {
        handleError('ID de réservation requis');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            SELECT b.*,
                   st.name as service_type_name,
                   vt.name as vehicle_type_name,
                   lc.name as luxury_category_name,
                   pc.name as pickup_city_name,
                   dc.name as dropoff_city_name,
                   d.first_name as driver_first_name,
                   d.last_name as driver_last_name,
                   d.phone as driver_phone,
                   d.avatar as driver_avatar,
                   dp.rating as driver_rating,
                   v.brand as vehicle_brand,
                   v.model as vehicle_model,
                   v.license_plate as vehicle_plate,
                   v.color as vehicle_color,
                   v.photos as vehicle_photos
            FROM bookings b
            JOIN service_types st ON b.service_type_id = st.id
            JOIN vehicle_types vt ON b.vehicle_type_id = vt.id
            JOIN luxury_categories lc ON b.luxury_category_id = lc.id
            JOIN cities pc ON b.pickup_city_id = pc.id
            JOIN cities dc ON b.dropoff_city_id = dc.id
            LEFT JOIN users d ON b.assigned_driver_id = d.id
            LEFT JOIN driver_profiles dp ON d.id = dp.user_id
            LEFT JOIN vehicles v ON b.assigned_vehicle_id = v.id
            WHERE b.id = ? AND b.client_id = ?
        ");
        $stmt->execute([$bookingId, $user['id']]);
        $booking = $stmt->fetch();

        if (!$booking) {
            handleError('Réservation non trouvée', 404);
        }

        // Récupérer l'historique des statuts
        $stmt = $pdo->prepare("
            SELECT * FROM booking_status_history
            WHERE booking_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->execute([$bookingId]);
        $booking['status_history'] = $stmt->fetchAll();

        handleSuccess($booking);

    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de la réservation: " . $e->getMessage());
        handleError('Erreur lors de la récupération de la réservation', 500);
    }
}

/**
 * Annuler une réservation
 */
function handleCancelBooking($user) {
    $data = json_decode(file_get_contents('php://input'), true);
    $bookingId = $data['booking_id'] ?? null;
    $reason = $data['reason'] ?? '';

    if (!$bookingId) {
        handleError('ID de réservation requis');
    }

    $pdo = getDbConnection();

    try {
        // Récupérer la réservation
        $stmt = $pdo->prepare("
            SELECT * FROM bookings
            WHERE id = ? AND client_id = ?
        ");
        $stmt->execute([$bookingId, $user['id']]);
        $booking = $stmt->fetch();

        if (!$booking) {
            handleError('Réservation non trouvée', 404);
        }

        // Vérifier si la réservation peut être annulée
        if (in_array($booking['status'], ['completed', 'cancelled'])) {
            handleError('Cette réservation ne peut pas être annulée');
        }

        // Vérifier les conditions d'annulation gratuite
        $hoursDiff = (strtotime($booking['pickup_datetime']) - time()) / 3600;
        $cancellationFreeHours = 6; // Par défaut 6 heures

        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'cancellation_hours'");
        $stmt->execute();
        $setting = $stmt->fetch();
        if ($setting) {
            $cancellationFreeHours = (int)$setting['setting_value'];
        }

        $isFree = $hoursDiff >= $cancellationFreeHours;

        // Mettre à jour le statut
        $stmt = $pdo->prepare("
            UPDATE bookings
            SET status = 'cancelled', cancellation_reason = ?
            WHERE id = ?
        ");
        $stmt->execute([$reason, $bookingId]);

        // Logger l'activité
        logActivity($user['id'], 'cancel_booking', 'booking', $bookingId, [
            'reason' => $reason,
            'is_free' => $isFree
        ]);

        // Envoyer une notification
        sendNotification(
            $user['id'],
            'booking_cancelled',
            'Réservation annulée',
            "Votre réservation {$booking['booking_number']} a été annulée",
            ['booking_id' => $bookingId]
        );

        // Si un chauffeur était affecté, le notifier
        if ($booking['assigned_driver_id']) {
            sendNotification(
                $booking['assigned_driver_id'],
                'booking_cancelled',
                'Trajet annulé',
                "Le trajet {$booking['booking_number']} a été annulé par le client",
                ['booking_id' => $bookingId]
            );
        }

        handleSuccess([
            'booking_id' => $bookingId,
            'is_free_cancellation' => $isFree
        ], 'Réservation annulée avec succès');

    } catch (PDOException $e) {
        error_log("Erreur lors de l'annulation: " . $e->getMessage());
        handleError('Erreur lors de l\'annulation', 500);
    }
}

/**
 * Calculer le prix d'un trajet
 */
function handleCalculatePrice() {
    $data = json_decode(file_get_contents('php://input'), true);

    $required = ['pickup_city_id', 'dropoff_city_id', 'vehicle_type_id', 'luxury_category_id'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            handleError("Le champ $field est requis");
        }
    }

    $pdo = getDbConnection();

    try {
        // Récupérer les coordonnées
        $stmt = $pdo->prepare("SELECT latitude, longitude, name FROM cities WHERE id = ?");

        $stmt->execute([$data['pickup_city_id']]);
        $pickupCity = $stmt->fetch();

        $stmt->execute([$data['dropoff_city_id']]);
        $dropoffCity = $stmt->fetch();

        if (!$pickupCity || !$dropoffCity) {
            handleError('Ville invalide');
        }

        // Calculer la distance
        $distance = calculateDistance(
            $pickupCity['latitude'],
            $pickupCity['longitude'],
            $dropoffCity['latitude'],
            $dropoffCity['longitude']
        );

        // Calculer le prix
        $prices = calculateBookingPrice(
            $data['vehicle_type_id'],
            $data['luxury_category_id'],
            $distance
        );

        handleSuccess([
            'distance_km' => round($distance, 2),
            'estimated_duration_minutes' => round($distance * 1.2), // Estimation: 1.2 min par km
            'pickup_city' => $pickupCity['name'],
            'dropoff_city' => $dropoffCity['name'],
            'base_price' => $prices['base_price'],
            'distance_price' => $prices['distance_price'],
            'luxury_surcharge' => $prices['luxury_surcharge'],
            'total_price' => $prices['total_price'],
            'formatted_price' => formatPrice($prices['total_price'])
        ]);

    } catch (PDOException $e) {
        error_log("Erreur lors du calcul du prix: " . $e->getMessage());
        handleError('Erreur lors du calcul du prix', 500);
    }
}

/**
 * Calculer le prix d'une réservation
 */
function calculateBookingPrice($vehicleTypeId, $luxuryCategoryId, $distance) {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("SELECT base_price, price_per_km FROM vehicle_types WHERE id = ?");
    $stmt->execute([$vehicleTypeId]);
    $vehicleType = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT price_multiplier FROM luxury_categories WHERE id = ?");
    $stmt->execute([$luxuryCategoryId]);
    $luxuryCategory = $stmt->fetch();

    $basePrice = $vehicleType['base_price'];
    $distancePrice = $vehicleType['price_per_km'] * $distance;
    $subtotal = $basePrice + $distancePrice;
    $luxurySurcharge = $subtotal * ($luxuryCategory['price_multiplier'] - 1);
    $totalPrice = $subtotal + $luxurySurcharge;

    return [
        'base_price' => round($basePrice, 2),
        'distance_price' => round($distancePrice, 2),
        'luxury_surcharge' => round($luxurySurcharge, 2),
        'total_price' => round($totalPrice, 2)
    ];
}

/**
 * Obtenir les véhicules disponibles
 */
function handleAvailableVehicles() {
    $cityId = $_GET['city_id'] ?? null;
    $datetime = $_GET['datetime'] ?? null;
    $vehicleTypeId = $_GET['vehicle_type_id'] ?? null;

    $pdo = getDbConnection();

    try {
        $whereClause = "WHERE v.status = 'available'";
        $params = [];

        if ($vehicleTypeId) {
            $whereClause .= " AND v.vehicle_type_id = ?";
            $params[] = $vehicleTypeId;
        }

        $sql = "
            SELECT v.*, vt.name as type_name, lc.name as luxury_name
            FROM vehicles v
            JOIN vehicle_types vt ON v.vehicle_type_id = vt.id
            JOIN luxury_categories lc ON v.luxury_category_id = lc.id
            $whereClause
            ORDER BY lc.price_multiplier ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll();

        handleSuccess(['vehicles' => $vehicles]);

    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des véhicules: " . $e->getMessage());
        handleError('Erreur lors de la récupération des véhicules', 500);
    }
}

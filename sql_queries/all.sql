CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    email VARCHAR(120) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role ENUM(
        'Fleet Manager',
        'Driver',
        'Safety Officer',
        'Financial Analyst'
    ) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- TransitOps - Single Combined SQL File
-- For PHP + MySQL (XAMPP/WAMP) localhost setup
-- =====================================================

DROP DATABASE IF EXISTS transitops;
CREATE DATABASE transitops CHARACTER SET utf8mb4;
USE transitops;

-- =====================================================
-- TABLE: user1  (renamed from "users")
-- =====================================================
CREATE TABLE user1 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLE: vehicle_types
-- =====================================================
CREATE TABLE vehicle_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(30) UNIQUE NOT NULL,
  label VARCHAR(50) NOT NULL,
  icon VARCHAR(30),
  rate_per_km DECIMAL(6,2) NOT NULL
);

INSERT INTO vehicle_types (`key`, label, icon, rate_per_km) VALUES
('Bike','Bike','fa-motorcycle',5),
('Car Sedan','Car Sedan','fa-car',9),
('Car SUV','Car SUV','fa-truck-pickup',13),
('Truck','Truck','fa-truck',16);

-- =====================================================
-- TABLE: drivers
-- =====================================================
CREATE TABLE drivers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  license_number VARCHAR(30),
  license_category VARCHAR(20),
  safety_score INT DEFAULT 100,
  vehicle_reg VARCHAR(20),
  vehicle_name VARCHAR(50)
);

INSERT INTO drivers (name, phone, license_number, license_category, safety_score, vehicle_reg, vehicle_name) VALUES
('Ramesh Patel', '+919812345670', 'GJ01-2020-0123456', 'HMV', 92, 'GJ01AB1234', 'Tata Ace'),
('Suresh Kumar', '+919812345671', 'GJ01-2019-0654321', 'LMV', 88, 'GJ01CD5678', 'Mahindra Bolero');

-- =====================================================
-- TABLE: trips
-- =====================================================
CREATE TABLE trips (
  id VARCHAR(20) PRIMARY KEY,
  user_id INT,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  pickup VARCHAR(150) NOT NULL,
  `drop` VARCHAR(150) NOT NULL,
  trip_date DATE NOT NULL,
  trip_time TIME,
  cargo VARCHAR(50) NOT NULL,
  weight DECIMAL(8,2) NULL,
  distance DECIMAL(6,1) NOT NULL,
  vehicle_type VARCHAR(30),
  priority ENUM('Low','Medium','High') DEFAULT 'Medium',
  notes TEXT,
  fare DECIMAL(10,2) NOT NULL,
  status ENUM('Requested','Accepted','In Transit','Completed','Rejected','Cancelled') DEFAULT 'Requested',
  progress TINYINT DEFAULT 0,
  driver_id INT NULL,
  created_at DATETIME NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user1(id),
  FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- =====================================================
-- TABLE: notifications
-- =====================================================
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  trip_id VARCHAR(20),
  title VARCHAR(200) NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (trip_id) REFERENCES trips(id)
);

-- =====================================================
-- SAMPLE DATA
-- =====================================================
INSERT INTO user1 (name, phone) VALUES ('Priya Mehta', '+919876543210');

INSERT INTO trips
  (id, user_id, name, phone, pickup, `drop`, trip_date, trip_time, cargo, weight,
   distance, vehicle_type, priority, notes, fare, status, progress, driver_id, created_at)
VALUES
  ('TRP-2001', 1, 'Priya Mehta', '+919876543210', 'Vastrapur, Ahmedabad', 'Ring Road, Surat',
   '2026-07-15', '09:30:00', 'General Freight', 500, 265, 'Truck', 'Medium',
   'Handle with care', 4240, 'Accepted', 0, 1, NOW()),
  ('TRP-2002', 1, 'Priya Mehta', '+919876543210', 'SG Highway, Ahmedabad', 'Airport, Ahmedabad',
   '2026-07-12', '18:00:00', 'Passenger Transport', NULL, 14, 'Car Sedan', 'High',
   'Need on time', 126, 'Requested', 0, NULL, NOW());

-- =====================================================
-- QUERY 1: Book a new trip
-- =====================================================
-- INSERT INTO trips
--   (id, user_id, name, phone, pickup, `drop`, trip_date, trip_time, cargo, weight,
--    distance, vehicle_type, priority, notes, fare, status, progress, created_at)
-- VALUES
--   (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Requested', 0, NOW());

-- =====================================================
-- QUERY 2: Calculate fare using vehicle rate
-- =====================================================
-- SELECT t.distance * v.rate_per_km AS fare
-- FROM trips t
-- JOIN vehicle_types v ON v.`key` = t.vehicle_type
-- WHERE t.id = ?;

-- =====================================================
-- QUERY 3: Driver accepts a trip
-- =====================================================
-- UPDATE trips SET status = 'Accepted', driver_id = ? WHERE id = ?;

-- =====================================================
-- QUERY 4: Update trip progress (In Transit)
-- =====================================================
-- UPDATE trips SET status = 'In Transit', progress = LEAST(95, progress + 4) WHERE id = ?;

-- =====================================================
-- QUERY 5: Mark trip completed
-- =====================================================
-- UPDATE trips SET status = 'Completed', progress = 100 WHERE id = ?;

-- =====================================================
-- QUERY 6: Cancel a trip (user-side, only if pending/accepted)
-- =====================================================
-- UPDATE trips SET status = 'Cancelled' WHERE id = ? AND status IN ('Requested','Accepted');

-- =====================================================
-- QUERY 7: Dashboard stats for a user
-- =====================================================
-- SELECT
--   COUNT(*) AS total_trips,
--   SUM(status IN ('Accepted','In Transit')) AS active_trips,
--   SUM(status = 'Completed') AS completed_trips,
--   SUM(CASE WHEN status NOT IN ('Cancelled','Rejected') THEN fare ELSE 0 END) AS total_spend
-- FROM trips
-- WHERE user_id = ?;

-- =====================================================
-- QUERY 8: Status breakdown (for chart)
-- =====================================================
-- SELECT status, COUNT(*) AS cnt
-- FROM trips
-- WHERE user_id = ?
-- GROUP BY status;

-- =====================================================
-- QUERY 9: Recent trips (dashboard table, latest 5)
-- =====================================================
-- SELECT id, pickup, `drop`, status, trip_date
-- FROM trips
-- WHERE user_id = ?
-- ORDER BY trip_date DESC, created_at DESC
-- LIMIT 5;

-- =====================================================
-- QUERY 10: Trip history with search + filter + sort
-- =====================================================
-- SELECT t.*, d.name AS driver_name, d.phone AS driver_phone, d.vehicle_reg, d.vehicle_name
-- FROM trips t
-- LEFT JOIN drivers d ON d.id = t.driver_id
-- WHERE t.user_id = ?
--   AND (t.id LIKE CONCAT('%', ?, '%') OR t.pickup LIKE CONCAT('%', ?, '%') OR t.`drop` LIKE CONCAT('%', ?, '%'))
--   AND (? = 'all' OR t.status = ?)
-- ORDER BY
--   CASE WHEN ? = 'oldest' THEN t.trip_date END ASC,
--   CASE WHEN ? = 'newest' THEN t.trip_date END DESC,
--   CASE WHEN ? = 'priority' THEN FIELD(t.priority,'High','Medium','Low') END ASC;

-- =====================================================
-- QUERY 11: Track a single trip with driver info
-- =====================================================
-- SELECT t.*, d.name AS driver_name, d.phone AS driver_phone,
--        d.license_number, d.license_category, d.safety_score,
--        d.vehicle_reg, d.vehicle_name
-- FROM trips t
-- LEFT JOIN drivers d ON d.id = t.driver_id
-- WHERE t.id = ?;

-- =====================================================
-- QUERY 12: Notifications - fetch all for a user
-- =====================================================
-- SELECT n.* FROM notifications n
-- JOIN trips t ON t.id = n.trip_id
-- WHERE t.user_id = ?
-- ORDER BY n.created_at DESC;

-- =====================================================
-- QUERY 13: Notifications - insert new
-- =====================================================
-- INSERT INTO notifications (trip_id, title) VALUES (?, ?);

-- =====================================================
-- QUERY 14: Notifications - mark all read
-- =====================================================
-- UPDATE notifications n
-- JOIN trips t ON t.id = n.trip_id
-- SET n.is_read = 1
-- WHERE t.user_id = ?;

-- =====================================================
-- QUERY 15: Profile stats
-- =====================================================
-- SELECT
--   u.name, u.phone,
--   COUNT(t.id) AS total_trips,
--   SUM(t.status = 'Completed') AS completed_trips,
--   SUM(CASE WHEN t.status NOT IN ('Cancelled','Rejected') THEN t.fare ELSE 0 END) AS total_spend
-- FROM user1 u
-- LEFT JOIN trips t ON t.user_id = u.id
-- WHERE u.id = ?
-- GROUP BY u.id;

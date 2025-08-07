-- Sandy Beauty Nails Database Schema
-- Database: fix360_sandy

USE fix360_sandy;

-- Table: services
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL DEFAULT 60,
    price DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: manicurists
CREATE TABLE IF NOT EXISTS manicurists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    specialties TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: clients
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    cedula VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone)
);

-- Table: appointments
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_id INT NOT NULL,
    manicurist_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'paid', 'completed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_reference VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (manicurist_id) REFERENCES manicurists(id) ON DELETE SET NULL,
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_appointment_time (appointment_time),
    INDEX idx_status (status)
);

-- Table: admin_users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin', 'manicurist') NOT NULL,
    manicurist_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manicurist_id) REFERENCES manicurists(id) ON DELETE SET NULL
);

-- Table: business_hours
CREATE TABLE IF NOT EXISTS business_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week TINYINT NOT NULL, -- 1=Monday, 2=Tuesday, ..., 7=Sunday
    open_time TIME NOT NULL,
    close_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Table: blocked_slots
CREATE TABLE IF NOT EXISTS blocked_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time TIME NOT NULL,
    manicurist_id INT,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manicurist_id) REFERENCES manicurists(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (date, time, manicurist_id)
);

-- Insert sample data

-- Services
INSERT INTO services (name, description, duration_minutes, price) VALUES
('Manicure Básico', 'Manicure tradicional con esmaltado básico', 45, 15.00),
('Manicure Spa', 'Manicure con tratamiento hidratante y masaje', 60, 25.00),
('Pedicure Básico', 'Pedicure tradicional con esmaltado básico', 60, 20.00),
('Pedicure Spa', 'Pedicure con tratamiento hidratante y masaje', 75, 30.00),
('Uñas Acrílicas', 'Aplicación de uñas acrílicas con diseño', 120, 45.00),
('Uñas en Gel', 'Aplicación de uñas en gel con diseño', 90, 35.00);

-- Manicurists
INSERT INTO manicurists (name, email, phone, specialties) VALUES
('Sandy Rodríguez', 'sandy@sandybeauty.com', '809-555-0001', 'Uñas acrílicas, diseños artísticos'),
('María González', 'maria@sandybeauty.com', '809-555-0002', 'Manicure spa, tratamientos hidratantes'),
('Ana Martínez', 'ana@sandybeauty.com', '809-555-0003', 'Pedicure, cuidado de pies');

-- Business Hours (Monday to Saturday, 8:00 AM to 7:00 PM)
INSERT INTO business_hours (day_of_week, open_time, close_time) VALUES
(1, '08:00:00', '19:00:00'), -- Monday
(2, '08:00:00', '19:00:00'), -- Tuesday
(3, '08:00:00', '19:00:00'), -- Wednesday
(4, '08:00:00', '19:00:00'), -- Thursday
(5, '08:00:00', '19:00:00'), -- Friday
(6, '08:00:00', '19:00:00'); -- Saturday

-- Admin Users (password: 'admin123' hashed)
INSERT INTO admin_users (username, password, role, manicurist_id) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', NULL),
('sandy', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manicurist', 1),
('maria', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manicurist', 2),
('ana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manicurist', 3);

-- Sample clients (for testing)
INSERT INTO clients (phone, name, email, cedula) VALUES
('809-555-1001', 'Laura Pérez', 'laura@email.com', '001-1234567-8'),
('809-555-1002', 'Carmen López', 'carmen@email.com', '001-2345678-9'),
('809-555-1003', 'Rosa Jiménez', 'rosa@email.com', NULL);

-- Sample appointments (for testing)
INSERT INTO appointments (client_id, service_id, manicurist_id, appointment_date, appointment_time, status, total_amount, payment_method) VALUES
(1, 1, 1, '2024-01-15', '10:00:00', 'completed', 15.00, 'cash'),
(2, 3, 3, '2024-01-16', '14:00:00', 'confirmed', 20.00, 'mercado_pago'),
(3, 5, 1, '2024-01-17', '09:00:00', 'pending', 45.00, NULL);
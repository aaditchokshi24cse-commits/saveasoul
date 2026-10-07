-- =====================================================
-- SaveASoul - Blood Donation & Donor Management System
-- Database Schema (for local XAMPP use - includes CREATE DATABASE)
-- =====================================================

CREATE DATABASE IF NOT EXISTS saveasoul;
USE saveasoul;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor', 'recipient', 'admin') NOT NULL DEFAULT 'donor',
    phone VARCHAR(15) NOT NULL,
    city VARCHAR(50) NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    verification_note VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    age INT NOT NULL,
    weight INT NOT NULL,
    last_donation_date DATE DEFAULT NULL,
    next_eligible_date DATE DEFAULT NULL,
    is_available TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE blood_inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL UNIQUE,
    units_available INT NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE camps (
    camp_id INT AUTO_INCREMENT PRIMARY KEY,
    camp_name VARCHAR(150) NOT NULL,
    venue VARCHAR(200) NOT NULL,
    city VARCHAR(50) NOT NULL,
    camp_date DATE NOT NULL,
    camp_time TIME NOT NULL,
    organizer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    units_needed INT NOT NULL,
    urgency ENUM('Normal','Urgent','Critical') NOT NULL DEFAULT 'Normal',
    hospital_name VARCHAR(150) NOT NULL,
    city VARCHAR(50) NOT NULL,
    status ENUM('Pending','Fulfilled','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    donation_date DATE NOT NULL,
    units INT NOT NULL DEFAULT 1,
    camp_id INT DEFAULT NULL,
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id) ON DELETE CASCADE,
    FOREIGN KEY (camp_id) REFERENCES camps(camp_id) ON DELETE SET NULL
);

-- Default admin account (placeholder password hash - fix using generate_admin_password.php after import)
INSERT INTO users (full_name, email, password, role, phone, city, is_verified)
VALUES ('System Admin', 'admin@saveasoul.com', '$2y$10$8K1p/a0dURXAm7QQCkP.Y.g0GgFhpg9DDb8zFwMhY2j0y2r0e5Uxa', 'admin', '9999999999', 'Ahmedabad', 1);

INSERT INTO blood_inventory (blood_group, units_available) VALUES
('A+', 12), ('A-', 4), ('B+', 15), ('B-', 3),
('AB+', 6), ('AB-', 2), ('O+', 20), ('O-', 5);

INSERT INTO camps (camp_name, venue, city, camp_date, camp_time, organizer)
VALUES ('Indus University Blood Donation Drive', 'Indus University Campus, Rancharda', 'Ahmedabad', '2026-09-20', '10:00:00', 'Red Cross Society');

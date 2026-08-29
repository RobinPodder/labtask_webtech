-- =============================================================
-- Telemedicine++ — Database Schema
-- Run this in phpMyAdmin (http://localhost/phpmyadmin) → Import,
-- or via: mysql -u root -p < telemedicine_plus_plus.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS telemedicine_plus_plus
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE telemedicine_plus_plus;

-- -------------------------------------------------------------
-- 1. USERS — shared auth table for doctor / sitter / patient
--    (Visitor has no account; browses index.html publicly)
-- -------------------------------------------------------------
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('doctor','sitter','patient') NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 2. ROLE-SPECIFIC PROFILE TABLES (1-to-1 with users)
-- -------------------------------------------------------------
CREATE TABLE doctors (
  user_id           INT PRIMARY KEY,
  specialty         VARCHAR(100) NOT NULL,
  years_exp         INT DEFAULT 0,
  consultation_fee  DECIMAL(10,2) DEFAULT 0,
  followup_fee      DECIMAL(10,2) DEFAULT 0,
  rating            DECIMAL(2,1) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sitters (
  user_id         INT PRIMARY KEY,
  specialization  VARCHAR(100) NOT NULL,
  service_area    VARCHAR(100) NOT NULL,
  daily_rate      DECIMAL(10,2) DEFAULT 0,
  hourly_rate     DECIMAL(10,2) DEFAULT 0,
  rating          DECIMAL(2,1) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE patients (
  user_id     INT PRIMARY KEY,
  age         INT,
  blood_group VARCHAR(5),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 3. AVAILABILITY — used by doctor & sitter "Set Availability" panel
-- -------------------------------------------------------------
CREATE TABLE availability (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,                          -- doctor or sitter
  day_of_week  ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun') NOT NULL,
  start_time   TIME NOT NULL,
  end_time     TIME NOT NULL,
  is_available TINYINT(1) DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 4. APPOINTMENTS — doctor <-> patient bookings
-- -------------------------------------------------------------
CREATE TABLE appointments (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id    INT NOT NULL,
  patient_id   INT NOT NULL,
  scheduled_at DATETIME NOT NULL,
  type         ENUM('New Consultation','Follow-up') DEFAULT 'New Consultation',
  status       ENUM('upcoming','completed','cancelled') DEFAULT 'upcoming',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id)  REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 5. PAYMENTS — linked to appointments, "Make Payment" panel
-- -------------------------------------------------------------
CREATE TABLE payments (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL,
  amount         DECIMAL(10,2) NOT NULL,
  method         ENUM('net_banking','bkash','card') DEFAULT 'net_banking',
  status         ENUM('due','paid') DEFAULT 'due',
  paid_at        TIMESTAMP NULL,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 6. MESSAGES — "Send & Receive Message" (doctor/sitter/patient)
-- -------------------------------------------------------------
CREATE TABLE messages (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  sender_id   INT NOT NULL,
  receiver_id INT NOT NULL,
  body        TEXT NOT NULL,
  sent_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 7. SITTER PATIENT-STATUS LOGS — "Update Patient Status" panel
-- -------------------------------------------------------------
CREATE TABLE patient_status_logs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  sitter_id   INT NOT NULL,
  patient_id  INT NOT NULL,
  vitals_note VARCHAR(255),
  condition_status ENUM('stable','needs_attention','critical') DEFAULT 'stable',
  logged_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sitter_id)  REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- 8. VISITOR-FACING PUBLIC CATALOGS (no login required)
-- -------------------------------------------------------------
CREATE TABLE medicines (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(100) NOT NULL,
  category ENUM('fever','diabetes','vitamin') NOT NULL,
  description VARCHAR(255),
  price    DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE lab_tests (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  hospital      VARCHAR(150) NOT NULL,
  turnaround    VARCHAR(50),
  price         DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- =============================================================
-- SEED DATA — matches the mock data already shown in the frontend
-- Password for ALL seeded accounts is: "password123"
-- (hash generated with PHP password_hash('password123', PASSWORD_DEFAULT))
-- =============================================================

INSERT INTO users (name, email, phone, password_hash, role) VALUES
('Dr. Nusrat Jahan', 'nusrat.jahan@telemedplusplus.com', '01711000000', '$2b$12$uTgJWxXPVzT2CtdqbLM2l.e/NoZ6Cr3WROMvWApOqgJujBl9VY.nS', 'doctor'),
('Rina Begum',       'rina.begum@telemedplusplus.com',   '01822000000', '$2b$12$uTgJWxXPVzT2CtdqbLM2l.e/NoZ6Cr3WROMvWApOqgJujBl9VY.nS', 'sitter'),
('Abdul Karim',      'abdul.karim@telemedplusplus.com',  '01911000000', '$2b$12$uTgJWxXPVzT2CtdqbLM2l.e/NoZ6Cr3WROMvWApOqgJujBl9VY.nS', 'patient');

-- user_id 1 = Dr. Nusrat Jahan, 2 = Rina Begum, 3 = Abdul Karim
INSERT INTO doctors (user_id, specialty, years_exp, consultation_fee, followup_fee, rating) VALUES
(1, 'Cardiology', 9, 800, 400, 4.8);

INSERT INTO sitters (user_id, specialization, service_area, daily_rate, hourly_rate, rating) VALUES
(2, 'Elderly Care', 'Narayanganj', 1200, 150, 4.9);

INSERT INTO patients (user_id, age, blood_group) VALUES
(3, 52, 'B+');

INSERT INTO availability (user_id, day_of_week, start_time, end_time, is_available) VALUES
(1, 'Mon', '10:00:00', '14:00:00', 1),
(1, 'Wed', '16:00:00', '20:00:00', 1),
(1, 'Fri', '10:00:00', '13:00:00', 0),
(2, 'Mon', '09:00:00', '17:00:00', 1),
(2, 'Sat', '00:00:00', '23:59:00', 0);

INSERT INTO appointments (doctor_id, patient_id, scheduled_at, type, status) VALUES
(1, 3, '2026-08-29 10:00:00', 'Follow-up', 'upcoming'),
(1, 3, '2026-08-12 10:00:00', 'New Consultation', 'completed');

INSERT INTO payments (appointment_id, amount, method, status) VALUES
(1, 800, 'net_banking', 'due'),
(2, 500, 'net_banking', 'paid');

INSERT INTO messages (sender_id, receiver_id, body) VALUES
(1, 3, 'Please continue the current medication and monitor BP daily.'),
(3, 1, 'Thank you doctor, I will take the medicine as prescribed.');

INSERT INTO patient_status_logs (sitter_id, patient_id, vitals_note, condition_status) VALUES
(2, 3, 'BP 130/85, Temp 98.4F', 'stable');

INSERT INTO medicines (name, category, description, price) VALUES
('Napa Extra', 'fever', 'Paracetamol 500mg + Caffeine, Strip of 10', 30),
('Insulin Mixtard 30/70', 'diabetes', 'Novo Nordisk, 10ml vial', 380),
('Seclo 20mg', 'fever', 'Omeprazole, Strip of 14', 84),
('Centrum Silver', 'vitamin', 'Multivitamin, 30 tablets', 950);

INSERT INTO lab_tests (name, hospital, turnaround, price) VALUES
('Complete Blood Count', 'Square Hospital, Dhaka', '6 hrs', 500),
('Chest X-Ray', 'Popular Diagnostic, Narayanganj', '2 hrs', 700),
('Blood Sugar (Fasting)', 'Ibn Sina Diagnostic', '3 hrs', 150);

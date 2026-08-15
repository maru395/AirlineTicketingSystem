<?php
/**
 * createdb.php
 * Runs the airline_ticketing schema against MySQL.
 * NOTE: contains chk_passengers_dob CHECK (date_of_birth < CURRENT_DATE)
 * which MySQL 8 rejects (non-deterministic function in CHECK clause).
 * This script will stop with an error at that statement unless you
 * remove/fix it.
 */

// ---- connection settings: edit these for your environment ----
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
// Note: no database selected yet, since this script creates it.
// -----------------------------------------------------------------

$mysqli = new mysqli($db_host, $db_user, $db_pass);

if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = <<<'SQL'
/* ============================================================
AIRLINE TICKETING SYSTEM
Database: airline_ticketing
MySQL 8.x
============================================================ */
DROP DATABASE IF EXISTS airline_ticketing;

CREATE DATABASE airline_ticketing CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

USE airline_ticketing;

/* ============================================================
1. AIRLINES
============================================================ */
CREATE TABLE
    airlines (
        airline_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        airline_code VARCHAR(10) NOT NULL,
        airline_name VARCHAR(100) NOT NULL,
        country VARCHAR(100) NOT NULL,
        status ENUM ('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT uq_airlines_code UNIQUE (airline_code),
        CONSTRAINT uq_airlines_name UNIQUE (airline_name),
        INDEX idx_airlines_status (status),
        INDEX idx_airlines_country (country),
        INDEX idx_airlines_name (airline_name)
    ) ENGINE = InnoDB;

/* ============================================================
2. AIRPORTS
============================================================ */
CREATE TABLE
    airports (
        airport_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        airport_code CHAR(3) NOT NULL,
        airport_name VARCHAR(150) NOT NULL,
        city VARCHAR(100) NOT NULL,
        country VARCHAR(100) NOT NULL,
        CONSTRAINT uq_airports_code UNIQUE (airport_code),
        INDEX idx_airports_name (airport_name),
        INDEX idx_airports_city (city),
        INDEX idx_airports_country (country),
        INDEX idx_airports_city_country (city, country)
    ) ENGINE = InnoDB;

/* ============================================================
3. AIRCRAFT
============================================================ */
CREATE TABLE
    aircraft (
        aircraft_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        airline_id INT UNSIGNED NOT NULL,
        registration_no VARCHAR(30) NOT NULL,
        aircraft_model VARCHAR(100) NOT NULL,
        seat_capacity SMALLINT UNSIGNED NOT NULL,
        status ENUM ('ACTIVE', 'MAINTENANCE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT uq_aircraft_registration UNIQUE (registration_no),
        CONSTRAINT chk_aircraft_seat_capacity CHECK (seat_capacity > 0),
        CONSTRAINT fk_aircraft_airline FOREIGN KEY (airline_id) REFERENCES airlines (airline_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        INDEX idx_aircraft_airline (airline_id),
        INDEX idx_aircraft_status (status),
        INDEX idx_aircraft_model (aircraft_model),
        INDEX idx_aircraft_airline_status (airline_id, status)
    ) ENGINE = InnoDB;

/* ============================================================
4. PASSENGERS
============================================================ */
CREATE TABLE
    passengers (
        passenger_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        passport_no VARCHAR(30) NOT NULL,
        first_name VARCHAR(60) NOT NULL,
        middle_name VARCHAR(60) NULL,
        last_name VARCHAR(60) NOT NULL,
        date_of_birth DATE NOT NULL,
        gender ENUM ('MALE', 'FEMALE', 'OTHER') NOT NULL,
        email VARCHAR(150) NOT NULL,
        contact_number VARCHAR(30) NOT NULL,
        nationality VARCHAR(100) NOT NULL,
        status ENUM ('ACTIVE', 'INACTIVE', 'BLACKLISTED') NOT NULL DEFAULT 'ACTIVE',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT uq_passengers_passport UNIQUE (passport_no),
        CONSTRAINT uq_passengers_email UNIQUE (email),
        CONSTRAINT chk_passengers_email CHECK (email LIKE '%@%.%'),
        CONSTRAINT chk_passengers_dob CHECK (date_of_birth < CURRENT_DATE),
        INDEX idx_passengers_last_name (last_name),
        INDEX idx_passengers_first_last (first_name, last_name),
        INDEX idx_passengers_nationality (nationality),
        INDEX idx_passengers_status (status),
        INDEX idx_passengers_created_at (created_at)
    ) ENGINE = InnoDB;

/* ============================================================
5. FLIGHTS
============================================================ */
CREATE TABLE
    flights (
        flight_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        airline_id INT UNSIGNED NOT NULL,
        aircraft_id INT UNSIGNED NOT NULL,
        flight_number VARCHAR(20) NOT NULL,
        origin_airport_id INT UNSIGNED NOT NULL,
        destination_airport_id INT UNSIGNED NOT NULL,
        departure_datetime DATETIME NOT NULL,
        arrival_datetime DATETIME NOT NULL,
        flight_status ENUM (
            'SCHEDULED',
            'BOARDING',
            'DEPARTED',
            'ARRIVED',
            'DELAYED',
            'CANCELLED'
        ) NOT NULL DEFAULT 'SCHEDULED',
        CONSTRAINT fk_flights_airline FOREIGN KEY (airline_id) REFERENCES airlines (airline_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_flights_aircraft FOREIGN KEY (aircraft_id) REFERENCES aircraft (aircraft_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_flights_origin FOREIGN KEY (origin_airport_id) REFERENCES airports (airport_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_flights_destination FOREIGN KEY (destination_airport_id) REFERENCES airports (airport_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT chk_flights_airports CHECK (origin_airport_id <> destination_airport_id),
        CONSTRAINT chk_flights_datetime CHECK (arrival_datetime > departure_datetime),
        INDEX idx_flights_airline (airline_id),
        INDEX idx_flights_aircraft (aircraft_id),
        INDEX idx_flights_origin (origin_airport_id),
        INDEX idx_flights_destination (destination_airport_id),
        INDEX idx_flights_number (flight_number),
        INDEX idx_flights_status (flight_status),
        INDEX idx_flights_departure (departure_datetime),
        INDEX idx_flights_arrival (arrival_datetime),
        INDEX idx_flights_route (origin_airport_id, destination_airport_id),
        INDEX idx_flights_search (
            origin_airport_id,
            destination_airport_id,
            departure_datetime
        ),
        INDEX idx_flights_airline_departure (airline_id, departure_datetime)
    ) ENGINE = InnoDB;

/* ============================================================
6. SEATS
============================================================ */
CREATE TABLE
    seats (
        seat_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        aircraft_id INT UNSIGNED NOT NULL,
        seat_number VARCHAR(10) NOT NULL,
        seat_class ENUM ('ECONOMY', 'PREMIUM_ECONOMY', 'BUSINESS', 'FIRST') NOT NULL DEFAULT 'ECONOMY',
        CONSTRAINT fk_seats_aircraft FOREIGN KEY (aircraft_id) REFERENCES aircraft (aircraft_id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT uq_seat_aircraft_number UNIQUE (aircraft_id, seat_number),
        INDEX idx_seats_aircraft (aircraft_id),
        INDEX idx_seats_class (seat_class),
        INDEX idx_seats_aircraft_class (aircraft_id, seat_class)
    ) ENGINE = InnoDB;

/* ============================================================
7. BOOKINGS
============================================================ */
CREATE TABLE
    bookings (
        booking_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        passenger_id INT UNSIGNED NOT NULL,
        flight_id INT UNSIGNED NOT NULL,
        booking_reference VARCHAR(20) NOT NULL,
        booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        booking_status ENUM ('PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED') NOT NULL DEFAULT 'PENDING',
        CONSTRAINT uq_booking_reference UNIQUE (booking_reference),
        CONSTRAINT fk_bookings_passenger FOREIGN KEY (passenger_id) REFERENCES passengers (passenger_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_bookings_flight FOREIGN KEY (flight_id) REFERENCES flights (flight_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        INDEX idx_bookings_passenger (passenger_id),
        INDEX idx_bookings_flight (flight_id),
        INDEX idx_bookings_status (booking_status),
        INDEX idx_bookings_date (booking_date),
        INDEX idx_bookings_passenger_status (passenger_id, booking_status),
        INDEX idx_bookings_flight_status (flight_id, booking_status)
    ) ENGINE = InnoDB;

/* ============================================================
8. TICKETS
============================================================ */
CREATE TABLE
    tickets (
        ticket_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        booking_id INT UNSIGNED NOT NULL,
        seat_id INT UNSIGNED NOT NULL,
        ticket_number VARCHAR(30) NOT NULL,
        fare DECIMAL(12, 2) NOT NULL,
        ticket_status ENUM ('ISSUED', 'USED', 'CANCELLED', 'REFUNDED') NOT NULL DEFAULT 'ISSUED',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT uq_ticket_number UNIQUE (ticket_number),
        CONSTRAINT fk_tickets_booking FOREIGN KEY (booking_id) REFERENCES bookings (booking_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_tickets_seat FOREIGN KEY (seat_id) REFERENCES seats (seat_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT chk_ticket_fare CHECK (fare >= 0),
        INDEX idx_tickets_booking (booking_id),
        INDEX idx_tickets_seat (seat_id),
        INDEX idx_tickets_status (ticket_status),
        INDEX idx_tickets_booking_status (booking_id, ticket_status),
        INDEX idx_tickets_seat_status (seat_id, ticket_status)
    ) ENGINE = InnoDB;

/* ============================================================
9. PAYMENTS
============================================================ */
CREATE TABLE
    payments (
        payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        booking_id INT UNSIGNED NOT NULL,
        payment_reference VARCHAR(50) NOT NULL,
        amount DECIMAL(12, 2) NOT NULL,
        payment_method ENUM (
            'CASH',
            'CREDIT_CARD',
            'DEBIT_CARD',
            'BANK_TRANSFER',
            'GCASH',
            'MAYA'
        ) NOT NULL,
        payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        payment_status ENUM ('PENDING', 'PAID', 'FAILED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
        CONSTRAINT uq_payment_reference UNIQUE (payment_reference),
        CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings (booking_id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT chk_payment_amount CHECK (amount >= 0),
        INDEX idx_payments_booking (booking_id),
        INDEX idx_payments_status (payment_status),
        INDEX idx_payments_date (payment_date),
        INDEX idx_payments_booking_status (booking_id, payment_status),
        INDEX idx_payments_method (payment_method)
    ) ENGINE = InnoDB;
SQL;

// mysqli_multi_query lets us run the whole multi-statement script at once.
if ($mysqli->multi_query($sql)) {
    $stmt_num = 1;
    do {
        // flush each result set (needed to advance through multi_query)
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
        if ($mysqli->errno) {
            echo "Error on statement #$stmt_num: " . $mysqli->error . "\n";
            break;
        }
        $stmt_num++;
    } while ($mysqli->more_results() && $mysqli->next_result());

    if (!$mysqli->errno) {
        echo "Database and tables created successfully.\n";
    }
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$mysqli->close();
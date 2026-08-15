<?php
declare(strict_types=1);
$host = "localhost";
$dbname = "airline_ticketing";
$username = "root";
$password = "";
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "Database connection successful.<br>";
    $pdo->exec("DELETE FROM payments");
    $pdo->exec("DELETE FROM tickets");
    $pdo->exec("DELETE FROM bookings");
    $pdo->exec("DELETE FROM seats");
    $pdo->exec("DELETE FROM flights");
    $pdo->exec("DELETE FROM passengers");
    $pdo->exec("DELETE FROM aircraft");
    $pdo->exec("DELETE FROM airlines");
    $pdo->exec("DELETE FROM airports");
    $pdo->exec("ALTER TABLE payments AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE tickets AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE bookings AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE seats AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE flights AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE passengers AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE aircraft AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE airlines AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE airports AUTO_INCREMENT = 1");
    $airlines = [
        ["PAL", "Philippine Airlines", "Philippines"],
        ["CEB", "Cebu Pacific", "Philippines"],
        ["AAL", "American Airlines", "United States"],
        ["UAL", "United Airlines", "United States"],
        ["DAL", "Delta Air Lines", "United States"],
        ["SIA", "Singapore Airlines", "Singapore"],
        ["JAL", "Japan Airlines", "Japan"],
        ["ANA", "All Nippon Airways", "Japan"],
        ["EK", "Emirates", "United Arab Emirates"],
        ["QFA", "Qantas", "Australia"]
    ];
    $stmt = $pdo->prepare("
 INSERT INTO airlines
 (
 airline_code,
 airline_name,
 country,
 status
 )
 VALUES
 (
 :code,
 :name,
 :country,
 'ACTIVE'
 )
 ");
    $airlineIds = [];
    foreach ($airlines as $airline) {
        $stmt->execute([
            ":code" => $airline[0],
            ":name" => $airline[1],
            ":country" => $airline[2]
        ]);
        $airlineIds[] = (int) $pdo->lastInsertId();
    }
    echo "Airlines created: " . count($airlineIds) . "<br>";
    $airports = [
        ["MNL", "Ninoy Aquino International Airport", "Manila", "Philippines"],
        ["CEB", "Mactan-Cebu International Airport", "Cebu", "Philippines"],
        ["DVO", "Francisco Bangoy International Airport", "Davao", "Philippines"],
        ["CRK", "Clark International Airport", "Clark", "Philippines"],
        ["ILO", "Iloilo International Airport", "Iloilo", "Philippines"],
        ["KLO", "Kalibo International Airport", "Kalibo", "Philippines"],
        ["HKG", "Hong Kong International Airport", "Hong Kong", "Hong Kong"],
        ["SIN", "Singapore Changi Airport", "Singapore", "Singapore"],
        ["NRT", "Narita International Airport", "Tokyo", "Japan"],
        ["HND", "Haneda Airport", "Tokyo", "Japan"],
        ["KIX", "Kansai International Airport", "Osaka", "Japan"],
        ["ICN", "Incheon International Airport", "Seoul", "South Korea"],
        ["DXB", "Dubai International Airport", "Dubai", "UAE"],
        ["LAX", "Los Angeles International Airport", "Los Angeles", "USA"],
        ["SFO", "San Francisco International Airport", "San Francisco", "USA"],
        ["JFK", "John F. Kennedy International Airport", "New York", "USA"],
        ["SYD", "Sydney Kingsford Smith Airport", "Sydney", "Australia"],
        ["BKK", "Suvarnabhumi Airport", "Bangkok", "Thailand"],
        ["KUL", "Kuala Lumpur International Airport", "Kuala Lumpur", "Malaysia"],
        ["TPE", "Taiwan Taoyuan International Airport", "Taipei", "Taiwan"]
    ];
    $stmt = $pdo->prepare("
 INSERT INTO airports
 (
 airport_code,
 airport_name,
 city,
 country
 )
 VALUES
 (
 :code,
 :name,
 :city,
 :country
 )
 ");
    $airportIds = [];
    foreach ($airports as $airport) {
        $stmt->execute([
            ":code" => $airport[0],
            ":name" => $airport[1],
            ":city" => $airport[2],
            ":country" => $airport[3]
        ]);
        $airportIds[] = (int) $pdo->lastInsertId();
    }
    echo "Airports created: " . count($airportIds) . "<br>";
    $aircraftModels = [
        ["Airbus A320", 180],
        ["Airbus A321", 220],
        ["Boeing 737-800", 189],
        ["Boeing 737 MAX 8", 178],
        ["Airbus A330-300", 440],
        ["Boeing 777-300ER", 396],
        ["Boeing 787-9", 296]
    ];
    $stmt = $pdo->prepare("
 INSERT INTO aircraft
 (
 airline_id,
 registration_no,
 aircraft_model,
 seat_capacity,
 status
 )
 VALUES
 (
 :airline_id,
 :registration,
 :model,
 :capacity,
 'ACTIVE'
 )
 ");
    $aircraftIds = [];
    for ($i = 1; $i <= 30; $i++) {
        $airlineId = $airlineIds[array_rand($airlineIds)];
        $model = $aircraftModels[array_rand($aircraftModels)];
        $registration = "RP-" . str_pad(
            (string) $i,
            4,
            "0",
            STR_PAD_LEFT
        );
        $stmt->execute([
            ":airline_id" => $airlineId,
            ":registration" => $registration,
            ":model" => $model[0],
            ":capacity" => $model[1]
        ]);
        $aircraftIds[] = [
            "id" => (int) $pdo->lastInsertId(),
            "airline" => $airlineId,
            "capacity" => $model[1]
        ];
    }
    echo "Aircraft created: " . count($aircraftIds) . "<br>";
    $stmt = $pdo->prepare("
 INSERT INTO seats
 (
 aircraft_id,
 seat_number,
 seat_class
 )
 VALUES
 (
 :aircraft_id,
 :seat_number,
 :seat_class
 )
 ");
    $seatIds = [];
    foreach ($aircraftIds as $aircraft) {
        $seatCount = min($aircraft["capacity"], 50);
        for ($seat = 1; $seat <= $seatCount; $seat++) {
            $row = (int) ceil($seat / 6);
            $letters = [
                "A",
                "B",
                "C",
                "D",
                "E",
                "F"
            ];
            $letter = $letters[($seat - 1) % 6];
            $seatNumber = $row . $letter;
            if ($row <= 3) {
                $seatClass = "BUSINESS";
            } else {
                $seatClass = "ECONOMY";
            }
            $stmt->execute([
                ":aircraft_id" => $aircraft["id"],
                ":seat_number" => $seatNumber,
                ":seat_class" => $seatClass
            ]);
            $seatIds[] = [
                "id" => (int) $pdo->lastInsertId(),
                "aircraft_id" => $aircraft["id"]
            ];
        }
    }
    echo "Seats created: " . count($seatIds) . "<br>";
    $stmt = $pdo->prepare("
 INSERT INTO flights
 (
 airline_id,
 aircraft_id,
 flight_number,
 origin_airport_id,
 destination_airport_id,
 departure_datetime,
 arrival_datetime,
 flight_status
 )
 VALUES
 (
 :airline_id,
 :aircraft_id,
 :flight_number,
 :origin,
 :destination,
 :departure,
 :arrival,
 :status
 )
 ");
    $flightIds = [];
    for ($i = 1; $i <= 100; $i++) {
        $aircraft = $aircraftIds[array_rand($aircraftIds)];
        $airlineId = $aircraft["airline"];
        $origin = $airportIds[array_rand($airportIds)];
        do {
            $destination = $airportIds[array_rand($airportIds)];
        } while ($destination === $origin);
        $departure = new DateTime();
        $departure->modify("+" . rand(1, 60) . " days");
        $departure->setTime(
            rand(0, 23),
            rand(0, 5) * 10,
            0
        );
        $durationHours = rand(1, 12);
        $arrival = clone $departure;
        $arrival->modify("+{$durationHours} hours");
        $flightNumber =
            strtoupper(
                "FL" . str_pad(
                    (string) $i,
                    4,
                    "0",
                    STR_PAD_LEFT
                )
            );
        $stmt->execute([
            ":airline_id" => $airlineId,
            ":aircraft_id" => $aircraft["id"],
            ":flight_number" => $flightNumber,
            ":origin" => $origin,
            ":destination" => $destination,
            ":departure" => $departure->format("Y-m-d H:i:s"),
            ":arrival" => $arrival->format("Y-m-d H:i:s"),
            ":status" => "SCHEDULED"
        ]);
        $flightIds[] = [
            "id" => (int) $pdo->lastInsertId(),
            "aircraft_id" => $aircraft["id"]
        ];
    }
    echo "Flights created: " . count($flightIds) . "<br>";
    $firstNames = [
        "Juan",
        "Maria",
        "Jose",
        "Ana",
        "Carlos",
        "Miguel",
        "Daniel",
        "John",
        "Michael",
        "James",
        "Robert",
        "David",
        "William",
        "Mark",
        "Peter",
        "Sofia",
        "Angela",
        "Grace",
        "Emily",
        "Sarah"
    ];
    $lastNames = [
        "Santos",
        "Reyes",
        "Cruz",
        "Garcia",
        "Dela Cruz",
        "Mendoza",
        "Torres",
        "Gonzales",
        "Bautista",
        "Navarro",
        "Rivera",
        "Aquino",
        "Castillo",
        "Ramos",
        "Flores"
    ];
    $nationalities = [
        "Filipino",
        "American",
        "Japanese",
        "Singaporean",
        "Korean",
        "Australian",
        "British"
    ];
    $stmt = $pdo->prepare("
 INSERT INTO passengers
 (
 passport_no,
 first_name,
 middle_name,
 last_name,
 date_of_birth,
 gender,
 email,
 contact_number,
 nationality,
 status
 )
 VALUES
 (
 :passport,
 :first_name,
 :middle_name,
 :last_name,
 :dob,
 :gender,
 :email,
 :contact,
 :nationality,
 'ACTIVE'
 )
 ");
    $passengerIds = [];
    for ($i = 1; $i <= 500; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $middleName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $gender = (rand(0, 1) === 0)
            ? "MALE"
            : "FEMALE";
        $year = rand(1960, 2005);
        $month = rand(1, 12);
        $day = rand(1, 28);
        $dob = sprintf(
            "%04d-%02d-%02d",
            $year,
            $month,
            $day
        );
        $passport =
            "P" .
            date("ym") .
            str_pad(
                (string) $i,
                6,
                "0",
                STR_PAD_LEFT
            );
        $email =
            strtolower($firstName) .
            "." .
            strtolower($lastName) .
            $i .
            "@example.com";
        $contact =
            "+639" .
            rand(100000000, 999999999);
        $nationality =
            $nationalities[array_rand($nationalities)];
        $stmt->execute([
            ":passport" => $passport,
            ":first_name" => $firstName,
            ":middle_name" => $middleName,
            ":last_name" => $lastName,
            ":dob" => $dob,
            ":gender" => $gender,
            ":email" => $email,
            ":contact" => $contact,
            ":nationality" => $nationality
        ]);
        $passengerIds[] =
            (int) $pdo->lastInsertId();
    }
    echo "Passengers created: " .
        count($passengerIds) .
        "<br>";
    $stmt = $pdo->prepare("
 INSERT INTO bookings
 (
 passenger_id,
 flight_id,
 booking_reference,
 booking_date,
 booking_status
 )
 VALUES
 (
 :passenger_id,
 :flight_id,
 :reference,
 :booking_date,
 :status
 )
 ");
    $bookingIds = [];
    for ($i = 1; $i <= 500; $i++) {
        $passengerId =
            $passengerIds[array_rand($passengerIds)];
        $flight =
            $flightIds[array_rand($flightIds)];
        $reference =
            "BK" .
            date("ym") .
            strtoupper(
                substr(
                    bin2hex(random_bytes(4)),
                    0,
                    6
                )
            );
        $bookingDate = new DateTime();
        $bookingDate->modify(
            "-" . rand(1, 30) . " days"
        );
        $statuses = [
            "PENDING",
            "CONFIRMED",
            "COMPLETED"
        ];
        $status =
            $statuses[array_rand($statuses)];
        $stmt->execute([
            ":passenger_id" => $passengerId,
            ":flight_id" => $flight["id"],
            ":reference" => $reference,
            ":booking_date" =>
                $bookingDate->format(
                    "Y-m-d H:i:s"
                ),
            ":status" => $status
        ]);
        $bookingIds[] = [
            "id" => (int) $pdo->lastInsertId(),
            "flight_id" => $flight["id"],
            "aircraft_id" => $flight["aircraft_id"]
        ];
    }
    echo "Bookings created: " .
        count($bookingIds) .
        "<br>";
    $seatsByAircraft = [];
    foreach ($seatIds as $seat) {
        $seatsByAircraft[$seat["aircraft_id"]][] =
            $seat["id"];
    }
    $stmt = $pdo->prepare("
 INSERT INTO tickets
 (
 booking_id,
 seat_id,
 ticket_number,
 fare,
 ticket_status
 )
 VALUES
 (
 :booking_id,
 :seat_id,
 :ticket_number,
 :fare,
 :status
 )
 ");
    $ticketIds = [];
    foreach ($bookingIds as $index => $booking) {
        $availableSeats =
            $seatsByAircraft[$booking["aircraft_id"]]
            ?? [];
        if (empty($availableSeats)) {
            continue;
        }
        $seatId =
            $availableSeats[
                array_rand($availableSeats)
            ];
        $ticketNumber =
            "TKT" .
            date("Y") .
            str_pad(
                (string) ($index + 1),
                7,
                "0",
                STR_PAD_LEFT
            );
        $fare = rand(2500, 35000);
        $ticketStatuses = [
            "ISSUED",
            "USED",
            "CANCELLED"
        ];
        $ticketStatus =
            $ticketStatuses[
                array_rand($ticketStatuses)
            ];
        $stmt->execute([
            ":booking_id" => $booking["id"],
            ":seat_id" => $seatId,
            ":ticket_number" => $ticketNumber,
            ":fare" => $fare,
            ":status" => $ticketStatus
        ]);
        $ticketIds[] =
            (int) $pdo->lastInsertId();
    }
    echo "Tickets created: " .
        count($ticketIds) .
        "<br>";
    $stmt = $pdo->prepare("
 INSERT INTO payments
 (
 booking_id,
 payment_reference,
 amount,
 payment_method,
 payment_date,
 payment_status
 )
 VALUES
 (
 :booking_id,
 :reference,
 :amount,
 :method,
 :payment_date,
 :status
 )
 ");
    $paymentMethods = [
        "CASH",
        "CREDIT_CARD",
        "DEBIT_CARD",
        "BANK_TRANSFER",
        "GCASH",
        "MAYA"
    ];
    $paymentStatuses = [
        "PAID",
        "PAID",
        "PAID",
        "PENDING",
        "FAILED"
    ];
    $paymentCount = 0;
    foreach ($bookingIds as $index => $booking) {
        $reference =
            "PAY" .
            date("Y") .
            str_pad(
                (string) ($index + 1),
                7,
                "0",
                STR_PAD_LEFT
            );
        $amount = rand(2500, 35000);
        $paymentDate = new DateTime();
        $paymentDate->modify(
            "-" . rand(0, 30) . " days"
        );
        $paymentMethod =
            $paymentMethods[
                array_rand($paymentMethods)
            ];
        $paymentStatus =
            $paymentStatuses[
                array_rand($paymentStatuses)
            ];
        $stmt->execute([
            ":booking_id" =>
                $booking["id"],
            ":reference" =>
                $reference,
            ":amount" =>
                $amount,
            ":method" =>
                $paymentMethod,
            ":payment_date" =>
                $paymentDate->format(
                    "Y-m-d H:i:s"
                ),
            ":status" =>
                $paymentStatus
        ]);
        $paymentCount++;
    }
    echo "Payments created: " .
        $paymentCount .
        "<br>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<hr>";
    echo "<strong>";
    echo "Database population completed successfully.";
    echo "</strong>";
} catch (PDOException $e) {

    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Throwable $ignored) {
    }
    echo "<strong>Database Error:</strong> ";
    echo htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>
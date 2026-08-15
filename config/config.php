<?php
class Config
{
    private PDO $pdo;
    private $lastError;

    public function __construct()
    {
        $host     = "localhost";
        $dbname   = "airline_ticketing";
        $username = "root";
        $password = "";

        try {
            $this->pdo = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("A system error occurred. Please try again later.");
        }
    }

    // passport number
    public function searchPassengerByPassport(string $q, int $excludeId = 0) {
        try {
            $sql = "
                SELECT COUNT(*) 
                FROM passengers 
                WHERE passport_no = :q";

            if ($excludeId > 0) {
                $sql .= " AND passenger_id != :exclude_id";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':q', $q, PDO::PARAM_STR);

            if ($excludeId > 0) {
                $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
            }

            $stmt->execute();

            $result = $stmt->fetchColumn();

            return (int)$result > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // email
    public function searchPassengerByEmail(string $q, int $excludeId = 0) {
        try {
            $sql = "
                SELECT COUNT(*) 
                FROM passengers 
                WHERE email = :q";

            if ($excludeId > 0) {
                $sql .= " AND passenger_id != :exclude_id";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':q', $q, PDO::PARAM_STR);

            if ($excludeId > 0) {
                $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
            }

            $stmt->execute();

            $result = $stmt->fetchColumn();

            return (int)$result > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // create passenger
    public function createPassenger(
        string $passport_no,
        string $first_name,
        string $middle_name,
        string $last_name,
        string $date_of_birth,
        string $gender,
        string $email,
        string $contact_number,
        string $nationality
    ) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO passengers (
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
                ) VALUES (
                    :passport_no,
                    :first_name,
                    :middle_name,
                    :last_name,
                    :date_of_birth,
                    :gender,
                    :email,
                    :contact_number,
                    :nationality,
                    'ACTIVE'
                )
            ");

            $stmt->bindParam(':passport_no', $passport_no, PDO::PARAM_STR);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':middle_name', $middle_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':date_of_birth', $date_of_birth, PDO::PARAM_STR);
            $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':contact_number', $contact_number, PDO::PARAM_STR);
            $stmt->bindParam(':nationality', $nationality, PDO::PARAM_STR);

            $stmt->execute();

            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getPassengerById(int $passengerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    passenger_id,
                    passport_no,
                    first_name,
                    middle_name,
                    last_name,
                    date_of_birth,
                    gender,
                    email,
                    contact_number,
                    nationality,
                    status,
                    created_at
                FROM passengers
                WHERE passenger_id = :passenger_id
                LIMIT 1
            ");

            $stmt->bindValue(':passenger_id', $passengerId, PDO::PARAM_INT);
            $stmt->execute();

            $passenger = $stmt->fetch();
            return $passenger ?: false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function updatePassenger(
        int $passengerId,
        string $passport_no,
        string $first_name,
        string $middle_name,
        string $last_name,
        string $date_of_birth,
        string $gender,
        string $email,
        string $contact_number,
        string $nationality
    ) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE passengers
                SET
                    passport_no = :passport_no,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    date_of_birth = :date_of_birth,
                    gender = :gender,
                    email = :email,
                    contact_number = :contact_number,
                    nationality = :nationality
                WHERE passenger_id = :passenger_id
            ");

            $stmt->bindValue(':passenger_id', $passengerId, PDO::PARAM_INT);
            $stmt->bindValue(':passport_no', $passport_no, PDO::PARAM_STR);
            $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindValue(':middle_name', $middle_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_birth', $date_of_birth, PDO::PARAM_STR);
            $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':contact_number', $contact_number, PDO::PARAM_STR);
            $stmt->bindValue(':nationality', $nationality, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // get all passengers
    public function getAllPassengers() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    passenger_id,
                    passport_no,
                    first_name,
                    middle_name,
                    last_name,
                    date_of_birth,
                    gender,
                    email,
                    contact_number,
                    nationality,
                    status,
                    created_at
                FROM passengers
                ORDER BY last_name, first_name
            ");

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->lastError = $e->getMessage();
            return false;
        }
    }

}
?>
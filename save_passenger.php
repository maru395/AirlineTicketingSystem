<?php

session_start();
require_once('config/config.php');
$db = new Config();


define('PASSPORT_REGEX', '/^[A-Z][0-9]{6,10}$/');
define('NAME_REGEX', '/^[A-Za-z ]{2,60}$/');
define('CONTACT_REGEX', '/^\+?[0-9]{10,13}$/');
define('NATIONALITY_REGEX', '/^[A-Za-z ]{2,100}$/');

function sanitizeInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    return $value;
}

function isValidDOB($dob) {
    $today = new DateTime();
    $birth = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$birth) {
        return false;
    }
    if ($birth > $today) {
        return false;
    }
    $age = $today->diff($birth)->y;
    return $age <= 120;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: passenger_form.php');
    exit;
}

$action         = isset($_POST['action']) ? strtolower(trim($_POST['action'])) : 'create';
$passenger_id   = isset($_POST['passenger_id']) ? (int) $_POST['passenger_id'] : 0;
$passport_no    = isset($_POST['passport_no']) ? trim($_POST['passport_no']) : '';
$gender         = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$date_of_birth  = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : '';
$first_name     = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$middle_name    = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$last_name      = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email          = isset($_POST['email']) ? trim($_POST['email']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$nationality    = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';

$old = [
    'passport_no'    => $passport_no,
    'gender'         => $gender,
    'date_of_birth'  => $date_of_birth,
    'first_name'     => $first_name,
    'middle_name'    => $middle_name,
    'last_name'      => $last_name,
    'email'          => $email,
    'contact_number' => $contact_number,
    'nationality'    => $nationality,
];

$errors = [];

if ($passport_no === '') {
    $errors[] = 'Passport number is required.';
} elseif (!preg_match(PASSPORT_REGEX, $passport_no)) {
    $errors[] = 'Passport number must be 1 capital letter followed by 6-10 digits.';
}

if ($gender === '') {
    $errors[] = 'Gender is required.';
} elseif (!in_array($gender, ['MALE', 'FEMALE', 'OTHER'])) {
    $errors[] = 'Invalid gender selected.';
}

if ($date_of_birth === '') {
    $errors[] = 'Date of birth is required.';
} elseif (!isValidDOB($date_of_birth)) {
    $errors[] = 'Invalid or unreasonable date of birth.';
}

if ($first_name === '') {
    $errors[] = 'First name is required.';
} elseif (!preg_match(NAME_REGEX, $first_name)) {
    $errors[] = 'First name must contain letters only.';
}

if ($middle_name !== '' && !preg_match(NAME_REGEX, $middle_name)) {
    $errors[] = 'Middle name must contain letters only.';
}

if ($last_name === '') {
    $errors[] = 'Last name is required.';
} elseif (!preg_match(NAME_REGEX, $last_name)) {
    $errors[] = 'Last name must contain letters only.';
}

if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

if ($contact_number === '') {
    $errors[] = 'Contact number is required.';
} elseif (!preg_match(CONTACT_REGEX, $contact_number)) {
    $errors[] = 'Contact number must be 10-13 digits, optional + prefix.';
}

if ($nationality === '') {
    $errors[] = 'Nationality is required.';
} elseif (!preg_match(NATIONALITY_REGEX, $nationality)) {
    $errors[] = 'Nationality must contain letters only.';
}

$passport_no    = strtoupper(sanitizeInput($passport_no));
$gender         = sanitizeInput($gender);
$date_of_birth  = sanitizeInput($date_of_birth);
$first_name     = sanitizeInput($first_name);
$middle_name    = sanitizeInput($middle_name);
$last_name      = sanitizeInput($last_name);
$email          = strtolower(sanitizeInput($email));
$contact_number = sanitizeInput($contact_number);
$nationality    = sanitizeInput($nationality);

if (empty($errors)) {
    if ($action === 'edit' && $passenger_id > 0) {
        if ($db->searchPassengerByPassport($passport_no, $passenger_id)) {
            $errors[] = 'This passport number is already registered.';
        }
        if ($db->searchPassengerByEmail($email, $passenger_id)) {
            $errors[] = 'This email is already registered.';
        }
    } else {
        if ($db->searchPassengerByPassport($passport_no)) {
            $errors[] = 'This passport number is already registered.';
        }
        if ($db->searchPassengerByEmail($email)) {
            $errors[] = 'This email is already registered.';
        }
    }
}

if (!empty($errors)) {
    $_SESSION['passenger_errors'] = $errors;
    $_SESSION['passenger_old'] = $old;
    $redirectUrl = $action === 'edit' && $passenger_id > 0
        ? 'passenger_form.php?id=' . $passenger_id
        : 'passenger_form.php';
    header('Location: ' . $redirectUrl);
    exit;
}

if ($action === 'edit' && $passenger_id > 0) {
    $result = $db->updatePassenger(
        $passenger_id,
        $passport_no,
        $first_name,
        $middle_name,
        $last_name,
        $date_of_birth,
        $gender,
        $email,
        $contact_number,
        $nationality
    );
    $message = 'Unable to update passenger. Please try again.';
} else {
    $result = $db->createPassenger(
        $passport_no,
        $first_name,
        $middle_name,
        $last_name,
        $date_of_birth,
        $gender,
        $email,
        $contact_number,
        $nationality
    );
    $message = 'Unable to register passenger. Please try again.';
}

if (!$result) {
    $_SESSION['passenger_errors'] = [$message];
    $_SESSION['passenger_old'] = $old;
    $redirectUrl = $action === 'edit' && $passenger_id > 0
        ? 'passenger_form.php?id=' . $passenger_id
        : 'passenger_form.php';
    header('Location: ' . $redirectUrl);
    exit;
}

header('Location: passengers.php');
exit;
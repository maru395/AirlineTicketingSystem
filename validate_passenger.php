<?php
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

if (isset($_GET['field']) && isset($_GET['value'])) {

    $field = $_GET['field'];
    $value = sanitizeInput($_GET['value']);
    $mode = isset($_GET['mode']) ? $_GET['mode'] : 'format';
    $message = '';
    $class = '';

    switch ($field) {

        case 'passport_no':
            if ($value === '') {
                $message = '';
            } elseif (!preg_match(PASSPORT_REGEX, $value)) {
                $message = 'Passport number must be 1 capital letter followed by 6-10 digits';
                $class = 'text-danger';
            } elseif ($mode === 'availability' && $db->searchPassengerByPassport($value)) {
                $message = 'This passport number is already registered';
                $class = 'text-danger';
            }
            break;

        case 'first_name':
        case 'middle_name':
        case 'last_name':
            if ($value === '') {
                $message = '';
            } elseif (!preg_match(NAME_REGEX, $value)) {
                $message = 'Alphabet only';
                $class = 'text-danger';
            }
            break;

        case 'gender':
            if ($value === '') {
                $message = '';
            } elseif (!in_array($value, ['MALE', 'FEMALE', 'OTHER'])) {
                $message = 'Invalid gender selected';
                $class = 'text-danger';
            }
            break;

        case 'date_of_birth':
            if ($value === '') {
                $message = '';
            } elseif (!isValidDOB($value)) {
                $message = 'Invalid or unreasonable date of birth';
                $class = 'text-danger';
            }
            break;

        case 'email':
            if ($value === '') {
                $message = '';
            } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $message = 'Invalid email format';
                $class = 'text-danger';
            } elseif ($mode === 'availability' && $db->searchPassengerByEmail($value)) {
                $message = 'This email is already registered';
                $class = 'text-danger';
            }
            break;

        case 'contact_number':
            if ($value === '') {
                $message = '';
            } elseif (!preg_match(CONTACT_REGEX, $value)) {
                $message = 'Numbers only, 10-13 digits (optional + prefix)';
                $class = 'text-danger';
            }
            break;

        case 'nationality':
            if ($value === '') {
                $message = '';
            } elseif (!preg_match(NATIONALITY_REGEX, $value)) {
                $message = 'Alphabet only';
                $class = 'text-danger';
            }
            break;

        default:
            $message = '';
            $class = '';
            break;
    }

    echo '<span class="' . $class . '">' . $message . '</span>';

} else {
    echo '';
}
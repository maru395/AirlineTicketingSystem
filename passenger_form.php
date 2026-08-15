<?php

session_start();
require_once('config/config.php');

$isEdit = false;
$passenger = null;

if (isset($_GET['id'])) {
    $passengerId = (int) $_GET['id'];
    if ($passengerId > 0) {
        $db = new Config();
        $passenger = $db->getPassengerById($passengerId);
        $isEdit = $passenger !== false;
    }
}

$errors = $_SESSION['passenger_errors'] ?? [];
$old = $_SESSION['passenger_old'] ?? [
    'passport_no' => '',
    'gender' => '',
    'date_of_birth' => '',
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'email' => '',
    'contact_number' => '',
    'nationality' => '',
];

if ($isEdit && empty($_SESSION['passenger_old'])) {
    $old = [
        'passport_no' => $passenger['passport_no'] ?? '',
        'gender' => $passenger['gender'] ?? '',
        'date_of_birth' => $passenger['date_of_birth'] ?? '',
        'first_name' => $passenger['first_name'] ?? '',
        'middle_name' => $passenger['middle_name'] ?? '',
        'last_name' => $passenger['last_name'] ?? '',
        'email' => $passenger['email'] ?? '',
        'contact_number' => $passenger['contact_number'] ?? '',
        'nationality' => $passenger['nationality'] ?? '',
    ];
}

unset($_SESSION['passenger_errors'], $_SESSION['passenger_old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Airline Ticketing and Passenger Management System">
    <meta name="author" content="IT 214 - Advanced Web Programming">

    <title><?php echo $isEdit ? 'Edit Passenger' : 'Register Passenger'; ?> | Airline Ticketing System</title>

    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Airline<sup>Ticketing</sup></div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="../dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Passenger Management</div>

            <li class="nav-item active">
                <a class="nav-link" href="../passengers.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Passengers</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Ticketing Operations</div>

            <li class="nav-item">
                <a class="nav-link" href="../bookings.php">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Bookings</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../tickets.php">
                    <i class="fas fa-fw fa-ticket-alt"></i>
                    <span>Tickets</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Analytics</div>

            <li class="nav-item">
                <a class="nav-link" href="../reports.php">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>JOIN Reports</span></a>
            </li>

        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <h5 class="d-none d-sm-inline-block m-0 text-gray-800 font-weight-bold"><?php echo $isEdit ? 'Edit Passenger' : 'Register Passenger'; ?></h5>
                </nav>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800"><?php echo $isEdit ? 'Edit Passenger' : 'Passenger Registration'; ?></h1>
                        <a href="passengers.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-arrow-left"></i> Back to list
                        </a>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <strong>Please fix the following before continuing:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errors as $fieldError): ?>
                                    <li><?php echo htmlspecialchars($fieldError, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Passenger Information</h6>
                        </div>
                        <div class="card-body">

                            <form id="passengerForm" method="POST" action="save_passenger.php"
                                onsubmit="return validateFormBeforeSubmit('passengerForm');" novalidate>

                                <input type="hidden" name="action" value="<?php echo $isEdit ? 'edit' : 'create'; ?>">
                                <?php if ($isEdit && !empty($passenger['passenger_id'])): ?>
                                    <input type="hidden" name="passenger_id" value="<?php echo (int) $passenger['passenger_id']; ?>">
                                <?php endif; ?>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="passport_no">Passport Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="passport_no" name="passport_no"
                                            maxlength="15" placeholder="e.g. P1234567"
                                            value="<?php echo htmlspecialchars($old['passport_no'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('passport_no', this.value, 'passportHint', 'format', false)"
                                            onblur="checkField('passport_no', this.value, 'passportHint', 'availability', true)"
                                            required>
                                        <small id="passportHint" class="small"></small>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select class="form-control" id="gender" name="gender"
                                            onchange="checkField('gender', this.value, 'genderHint', 'format', true)"
                                            required>
                                            <option value="">-- Select --</option>
                                            <option value="MALE" <?php echo $old['gender'] === 'MALE' ? 'selected' : ''; ?>>Male</option>
                                            <option value="FEMALE" <?php echo $old['gender'] === 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                            <option value="OTHER" <?php echo $old['gender'] === 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                        <small id="genderHint" class="small"></small>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="date_of_birth">Date of Birth <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                            value="<?php echo htmlspecialchars($old['date_of_birth'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onchange="checkField('date_of_birth', this.value, 'dobHint', 'format', true)"
                                            required>
                                        <small id="dobHint" class="small"></small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            maxlength="60"
                                            value="<?php echo htmlspecialchars($old['first_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('first_name', this.value, 'firstNameHint', 'format', false)"
                                            required>
                                        <small id="firstNameHint" class="small"></small>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name"
                                            maxlength="60"
                                            value="<?php echo htmlspecialchars($old['middle_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('middle_name', this.value, 'middleNameHint', 'format', false)">
                                        <small id="middleNameHint" class="small"></small>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            maxlength="60"
                                            value="<?php echo htmlspecialchars($old['last_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('last_name', this.value, 'lastNameHint', 'format', false)"
                                            required>
                                        <small id="lastNameHint" class="small"></small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" maxlength="150"
                                            placeholder="example@gmail.com"
                                            value="<?php echo htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('email', this.value, 'emailHint', 'format', false)"
                                            onblur="checkField('email', this.value, 'emailHint', 'availability', true)"
                                            required>
                                        <small id="emailHint" class="small"></small>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="contact_number">Contact Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact_number"
                                            name="contact_number" maxlength="30" placeholder="09123456789"
                                            value="<?php echo htmlspecialchars($old['contact_number'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('contact_number', this.value, 'contactHint', 'format', false)"
                                            required>
                                        <small id="contactHint" class="small"></small>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nationality">Nationality <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nationality" name="nationality"
                                            maxlength="100" placeholder="Filipino"
                                            value="<?php echo htmlspecialchars($old['nationality'], ENT_QUOTES, 'UTF-8'); ?>"
                                            onkeyup="checkField('nationality', this.value, 'nationalityHint', 'format', false)"
                                            required>
                                        <small id="nationalityHint" class="small"></small>
                                    </div>
                                </div>

                                <hr>

                                <button type="submit" id="submitBtn" name="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Passenger' : 'Register Passenger'; ?>
                                </button>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-eraser"></i> Clear
                                </button>
                            </form>

                        </div>
                    </div>

                </div><!-- /.container-fluid -->
            </div><!-- /#content -->

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Airline Ticketing and Passenger Management System</span>
                    </div>
                </div>
            </footer>
        </div><!-- /#content-wrapper -->
    </div><!-- /#wrapper -->

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- AJAX Validation (XMLHttpRequest only, no JSON, no fetch) -->
    <script src="assets/js/ajax.js"></script>

</body>

</html>
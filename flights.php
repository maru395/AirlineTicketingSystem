<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Airline Ticketing and Passenger Management System">
    <meta name="author" content="IT 214 - Advanced Web Programming">

    <title>Passengers | Airline Ticketing System</title>

    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="assets/css/passengers-layout.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-plane"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Airline<sup>Ticketing</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item ">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Passenger Management</div>

    <li class="nav-item">
        <a class="nav-link" href="passengers.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Passengers</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Ticketing Operations</div>

    <li class="nav-item">
        <a class="nav-link" href="bookings.php">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Bookings</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="flights.php">
            <i class="fas fa-fw fa-plane-departure"></i>
            <span>Flights</span></a>
    </li>

    <li class="nav-item ">
        <a class="nav-link" href="tickets.php">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Tickets</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Analytics</div>

    <li class="nav-item ">
        <a class="nav-link" href="reports.php">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>JOIN Reports</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <h5 class="d-none d-sm-inline-block m-0 text-gray-800 font-weight-bold">Flights</h5>

    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"></span>
                <img class="img-profile rounded-circle" src="assets/img/undraw_profile.svg">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<div class="container-fluid">

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Flight list</h1>
</div>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form class="form-inline" method="GET" action="passengers.php">
            <label class="mr-2 mb-0" for="q">Search:</label>
            <input type="text" class="form-control form-control-sm mr-2" id="q" name="q"
                   placeholder="Flights"
                   value="">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Search</button>
                    </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover data-table" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>Flight No.</th>
                        <th>Airline</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="9" class="text-center text-muted">No passengers found.</td></tr>
                </tbody>
            </table>
        </div>
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
<script src="assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
</body>
</html>
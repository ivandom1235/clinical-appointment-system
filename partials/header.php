<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$base = ''; // '' for clinical-booking.test root; '/clinical-booking' for subfolder
$pageCss = $pageCss ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clinic Booking</title>
  <link rel="icon" type="image/svg+xml"
        href='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="12" fill="%230097a7"/><text x="50%" y="54%" text-anchor="middle" font-family="Segoe UI, Arial" font-size="34" fill="white">CB</text></svg>'>

  <!-- base styles (shared) -->
  <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css" />

  <!-- page-specific style (optional) -->
  <?php if (!empty($pageCss)): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($pageCss) ?>" />
  <?php endif; ?>
</head>
<body>
  <header class="site-header">
    <h1>Clinic Booking</h1>
    <nav>
      <a href="<?= $base ?>/">Home</a>
      <?php if (!empty($_SESSION['uid'])): ?>
        <?php if ($_SESSION['role']==='patient'): ?>
          <a href="<?= $base ?>/patient/dashboard.php">Patient</a>
          <a href="<?= $base ?>/patient/appointments.php">My Appointments</a>
          <a href="<?= $base ?>/patient/book.php">Book</a>
        <?php elseif ($_SESSION['role']==='doctor'): ?>
          <a href="<?= $base ?>/doctor/dashboard.php">Doctor</a>
          <a href="<?= $base ?>/doctor/schedule.php">Schedule</a>
        <?php elseif ($_SESSION['role']==='staff'): ?>
          <a href="<?= $base ?>/staff/dashboard.php">Admin</a>
        <?php endif; ?>
        <a href="<?= $base ?>/auth/logout.php">Logout</a>
      <?php else: ?>
        <a href="<?= $base ?>/auth/login.php">Login</a>
        <a href="<?= $base ?>/auth/register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>
  <main class="container">

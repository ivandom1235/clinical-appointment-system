<?php

$pageCss = '/assets/css/pages/doctor_schedule.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('staff');

// Quick stats
$totalPatients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='patient'")->fetchColumn();
$totalDoctors  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='doctor'")->fetchColumn();
$totalAppts    = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2>Admin / Staff Dashboard</h2>
  <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>.</p>

  <div style="display:flex; gap:16px; flex-wrap:wrap;">
    <div class="card" style="flex:1;">
      <h3>📋 Patients</h3>
      <p>Total: <?= (int)$totalPatients ?></p>
    </div>
    <div class="card" style="flex:1;">
      <h3>👨‍⚕️ Doctors</h3>
      <p>Total: <?= (int)$totalDoctors ?></p>
      <p><a href="doctors.php">View Doctors</a> · <a href="add_doctor.php">➕ Add Doctor</a></p>

    </div>
    <div class="card" style="flex:1;">
      <h3>📅 Appointments</h3>
      <p>Total: <?= (int)$totalAppts ?></p>
      <p><a href="appointments.php?date=<?= date('Y-m-d') ?>">View Today</a></p>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

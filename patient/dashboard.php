<?php
$pageCss = '/assets/css/pages/patient_dashboard.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('patient');

// find or create patient profile for the logged-in user
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id=?");
$stmt->execute([$uid]);
$patient = $stmt->fetch();

if (!$patient) {
  $pdo->prepare("INSERT INTO patients (user_id) VALUES (?)")->execute([$uid]);
  $patient_id = (int)$pdo->lastInsertId();
} else {
  $patient_id = (int)$patient['id'];
}

// fetch upcoming appointments (none yet is fine)
$q = $pdo->prepare("
  SELECT a.*, u.name AS doctor_name
  FROM appointments a
  JOIN doctors d ON d.id = a.doctor_id
  JOIN users u ON u.id = d.user_id
  WHERE a.patient_id=? AND a.status='booked'
    AND (a.appt_date > CURDATE()
         OR (a.appt_date = CURDATE() AND a.appt_time >= CURTIME()))
  ORDER BY a.appt_date, a.appt_time
  LIMIT 20
");
$q->execute([$patient_id]);
$appts = $q->fetchAll();
?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="card">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
  <?php if (!empty($_GET['booked'])): ?>
  <p style="color:#22c55e;">✅ Appointment booked.</p>
<?php endif; ?>

  <p>Your upcoming appointments:</p>

  <?php if (!$appts): ?>
    <p><em>No upcoming appointments yet.</em></p>
    <p><a href="/patient/book.php">Book an appointment →</a></p>
  <?php else: ?>
    <table>
      <thead>
        <tr><th>Date</th><th>Time</th><th>Doctor</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($appts as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['appt_date']) ?></td>
            <td><?= htmlspecialchars(substr($a['appt_time'],0,5)) ?></td>
            <td>Dr. <?= htmlspecialchars($a['doctor_name']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

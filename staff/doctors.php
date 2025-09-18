<?php
$pageCss = '/assets/css/pages/staff_doctors.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('staff');

$q = $pdo->query("
  SELECT d.id AS doctor_id, u.name, u.email, d.specialty
  FROM doctors d JOIN users u ON u.id=d.user_id
  ORDER BY u.name
");
$rows = $q->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2>Doctors</h2>
  <?php if (!$rows): ?>
    <p><em>No doctors yet.</em></p>
  <?php else: ?>
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Specialty</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['specialty']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  <p style="margin-top:12px;">
    <a href="/staff/add_doctor.php"> Add Doctor</a> ·
    <a href="/staff/dashboard.php">Back</a>
  </p>
</section>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

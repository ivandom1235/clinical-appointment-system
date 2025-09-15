<?
$pageCss = '/clinic-booking/assets/css/pages/doctor_dashboard.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('doctor');

/* find doctor_id for this user */
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id=?");
$stmt->execute([$uid]);
$doctor_id = $stmt->fetchColumn();
if (!$doctor_id) { http_response_code(403); exit('No doctor profile'); }

/* Today’s appointments */
$today = date('Y-m-d');
$qToday = $pdo->prepare("
  SELECT a.*, u.name AS patient_name
  FROM appointments a
  JOIN patients p ON p.id = a.patient_id
  JOIN users u ON u.id = p.user_id
  WHERE a.doctor_id=? AND a.appt_date=? 
  ORDER BY a.appt_time
");
$qToday->execute([$doctor_id, $today]);
$todayList = $qToday->fetchAll();

/* Upcoming (from tomorrow) */
$qUpcoming = $pdo->prepare("
  SELECT a.*, u.name AS patient_name
  FROM appointments a
  JOIN patients p ON p.id = a.patient_id
  JOIN users u ON u.id = p.user_id
  WHERE a.doctor_id=? AND a.status='booked'
    AND a.appt_date > CURDATE()
  ORDER BY a.appt_date, a.appt_time
  LIMIT 25
");
$qUpcoming->execute([$doctor_id]);
$upcomingList = $qUpcoming->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2>Doctor Dashboard</h2>
  <?php if (!empty($_GET['updated'])): ?>
    <p style="color:#22c55e;">✅ Status updated.</p>
  <?php endif; ?>

  <h3>Today (<?= htmlspecialchars($today) ?>)</h3>
  <?php if (!$todayList): ?>
    <p><em>No appointments today.</em></p>
  <?php else: ?>
    <table>
      <thead>
        <tr><th>Time</th><th>Patient</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach ($todayList as $a): ?>
          <tr>
            <td><?= htmlspecialchars(substr($a['appt_time'],0,5)) ?></td>
            <td><?= htmlspecialchars($a['patient_name']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
            <td>
              <?php if ($a['status']==='booked'): ?>
                <form method="POST" action="/backend/doctor_update_status.php" style="display:inline">
                  <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
                  <input type="hidden" name="status" value="completed">
                  <button type="submit">Mark Completed</button>
                </form>
                <form method="POST" action="/backend/doctor_update_status.php" style="display:inline" onsubmit="return confirm('Mark as no-show?');">
                  <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
                  <input type="hidden" name="status" value="no_show">
                  <button type="submit">No Show</button>
                </form>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section class="card" style="margin-top:16px;">
  <h3>Upcoming</h3>
  <?php if (!$upcomingList): ?>
    <p><em>No upcoming bookings.</em></p>
  <?php else: ?>
    <table>
      <thead>
        <tr><th>Date</th><th>Time</th><th>Patient</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($upcomingList as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['appt_date']) ?></td>
            <td><?= htmlspecialchars(substr($a['appt_time'],0,5)) ?></td>
            <td><?= htmlspecialchars($a['patient_name']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

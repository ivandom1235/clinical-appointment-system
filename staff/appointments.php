<?php
$pageCss = '/assets/css/pages/staff_appointments.css'; 
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('staff');

$day = $_GET['date'] ?? date('Y-m-d');

// fetch all appointments for a day
$stmt = $pdo->prepare("
  SELECT a.*, 
         du.name AS doctor_name, 
         pu.name AS patient_name
  FROM appointments a
  JOIN doctors d   ON d.id = a.doctor_id
  JOIN users du    ON du.id = d.user_id
  JOIN patients p  ON p.id = a.patient_id
  JOIN users pu    ON pu.id = p.user_id
  WHERE a.appt_date = ?
  ORDER BY du.name, a.appt_time
");
$stmt->execute([$day]);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2>Appointments — <?= htmlspecialchars($day) ?></h2>
  <form method="get" style="margin-bottom:12px;">
    <label>Pick a date:
      <input type="date" name="date" value="<?= htmlspecialchars($day) ?>">
    </label>
    <button type="submit">Load</button>
    <a href="/staff/dashboard.php" style="margin-left:8px;">Back</a>
  </form>

  <?php if (!empty($_GET['cancelled'])): ?>
    <p style="color:#22c55e;"> Appointment cancelled.</p>
  <?php endif; ?>

  <?php if (!$rows): ?>
    <p><em>No appointments for this date.</em></p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Time</th>
          <th>Doctor</th>
          <th>Patient</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars(substr($r['appt_time'],0,5)) ?></td>
          <td>Dr. <?= htmlspecialchars($r['doctor_name']) ?></td>
          <td><?= htmlspecialchars($r['patient_name']) ?></td>
          <td><?= htmlspecialchars($r['status']) ?></td>

          <td>
            <?php if ($r['status']==='booked'): ?>
              <form method="POST" action="/backend/staff_cancel.php" onsubmit="return confirm('Cancel this appointment?');" style="display:inline;">
                <input type="hidden" name="appointment_id" value="<?= (int)$r['id'] ?>">
                <input type="hidden" name="reason" value="staff_cancel">
                <input type="hidden" name="redirect_date" value="<?= htmlspecialchars($day) ?>">
                <button type="submit">Cancel</button>
              </form>
              <!-- Optional: Reassign later
              <a href="/clinic-booking/staff/reassign.php?id=<?= (int)$r['id'] ?>">Reassign</a>
              -->
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
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

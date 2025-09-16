<?php
$pageCss = '/assets/css/pages/patient_appointments.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('patient');

/* ensure patient profile exists, get patient_id */
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id=?");
$stmt->execute([$uid]);
$patient_id = $stmt->fetchColumn();
if (!$patient_id) {
  $pdo->prepare("INSERT INTO patients (user_id) VALUES (?)")->execute([$uid]);
  $patient_id = (int)$pdo->lastInsertId();
}

/* fetch all appointments (recent first), show cancel only for future booked ones */
$q = $pdo->prepare("
  SELECT a.*, u.name AS doctor_name
  FROM appointments a
  JOIN doctors d ON d.id = a.doctor_id
  JOIN users u ON u.id = d.user_id
  WHERE a.patient_id=?
  ORDER BY a.appt_date DESC, a.appt_time DESC
");
$q->execute([$patient_id]);
$appts = $q->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2>My Appointments</h2>

  <?php if (!empty($_GET['cancelled'])): ?>
    <p style="color:#22c55e;">✅ Appointment cancelled.</p>
  <?php endif; ?>

  <?php if (!$appts): ?>
    <p><em>No appointments yet.</em></p>
    <p><a href="/clinic-booking/patient/book.php">Book an appointment →</a></p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Date</th><th>Time</th><th>Doctor</th><th>Status</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $nowDate = date('Y-m-d');
        $nowTime = date('H:i:s');
        foreach ($appts as $a):
          $isFuture = ($a['appt_date'] > $nowDate) ||
                      ($a['appt_date'] === $nowDate && $a['appt_time'] > $nowTime);
          $canCancel = ($a['status'] === 'booked') && $isFuture;
        ?>
          <tr>
            <td><?= htmlspecialchars($a['appt_date']) ?></td>
            <td><?= htmlspecialchars(substr($a['appt_time'],0,5)) ?></td>
            <td>Dr. <?= htmlspecialchars($a['doctor_name']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
            <td>
              <?php if ($canCancel): ?>
                <form method="POST" action="/backend/cancel_handler.php" onsubmit="return confirmCancel(this);">
                  <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
                  <input type="hidden" name="reason" value="patient_cancel">
                  <button type="submit">Cancel</button>
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

<script>
function confirmCancel(form){
  return confirm("Are you sure you want to cancel this appointment?");
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

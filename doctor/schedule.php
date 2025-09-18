<?php
$pageCss = '/assets/css/pages/doctor_schedule.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('doctor');

// get doctor_id
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id=?");
$stmt->execute([$uid]);
$doctor_id = $stmt->fetchColumn();
if (!$doctor_id) { http_response_code(403); exit('No doctor profile'); }

// fetch existing rows into an array keyed by weekday
$q = $pdo->prepare("SELECT weekday, TIME_FORMAT(start_time,'%H:%i') AS st, TIME_FORMAT(end_time,'%H:%i') AS en, slot_minutes AS sl
                    FROM doctor_schedules WHERE doctor_id=?");
$q->execute([$doctor_id]);
$rows = $q->fetchAll();
$byDay = array_fill(0, 7, ['st'=>'', 'en'=>'', 'sl'=>'30']); // defaults
foreach ($rows as $r) {
  $w = (int)$r['weekday'];
  $byDay[$w] = ['st'=>$r['st'], 'en'=>$r['en'], 'sl'=>$r['sl']];
}

$days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2>Weekly Schedule</h2>
  <?php if (!empty($_GET['saved'])): ?>
    <p style="color:#22c55e;"> Schedule saved.</p>
  <?php endif; ?>

  <form method="post" action="/backend/schedule_save.php">
    <table>
      <thead>
        <tr>
          <th>Day</th>
          <th>Enable</th>
          <th>Start (HH:MM)</th>
          <th>End (HH:MM)</th>
          <th>Slot (mins)</th>
        </tr>
      </thead>
      <tbody>
        <?php for ($w=0; $w<=6; $w++): 
          $have = $byDay[$w]['st'] && $byDay[$w]['en'];
        ?>
        <tr>
          <td><?= $days[$w] ?></td>
          <td><input type="checkbox" name="enable[<?= $w ?>]" <?= $have ? 'checked' : '' ?>></td>
          <td><input type="time" name="start[<?= $w ?>]" value="<?= htmlspecialchars($byDay[$w]['st']) ?>"></td>
          <td><input type="time" name="end[<?= $w ?>]" value="<?= htmlspecialchars($byDay[$w]['en']) ?>"></td>
          <td><input type="number" min="5" max="180" step="5" name="slot[<?= $w ?>]" value="<?= (int)$byDay[$w]['sl'] ?>"></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <div>
      <button type="submit">Save Schedule</button>
      <a href="/doctor/dashboard.php">Back</a>
    </div>
  </form>

  
</section>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

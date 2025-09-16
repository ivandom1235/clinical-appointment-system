<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_role('patient');

$doctorId = intval($_POST['doctor_id'] ?? 0);
$date     = $_POST['date'] ?? '';
$time     = $_POST['time'] ?? '';
$reason   = trim($_POST['reason'] ?? '');

if (!$doctorId || !$date || !$time) {
  exit('Missing fields');
}

$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id=?");
$stmt->execute([$uid]);
$patientId = $stmt->fetchColumn();

try {
  $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason)
                         VALUES (?,?,?,?, 'booked', ?)");
  $stmt->execute([$patientId, $doctorId, $date, $time, $reason]);
  header("Location: /patient/dashboard.php?booked=1");
  exit;
} catch (PDOException $e) {
  if ($e->getCode()==='23000') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>Slot Already Taken</title>
      <style>
        body { font-family: Segoe UI, Arial, sans-serif; background: #f9f9f9; }
        .card { max-width: 400px; margin: 80px auto; padding: 2rem 2rem 1.2rem; background: white; border-radius: 16px; box-shadow: 0 2px 18px #0001; text-align: center; }
        h2 { color: #c00; margin-bottom: 1.1rem; }
        button, .btn { background: #0097a7; color: #fff; border: none; padding: 0.7em 1.6em; font-size: 1em; border-radius: 8px; cursor: pointer; margin-top: 1.3em;}
        button:hover, .btn:hover { background: #007e8c; }
      </style>
    </head>
    <body>
      <div class="card">
        <h2>Slot Already Taken</h2>
        <p>Sorry, the selected slot is not available.<br>Please choose a different time.</p>
        <button onclick="history.back()">Go Back</button>
        <!-- Or you could use: <a href="/patient/book.php" class="btn">Go to Booking Page</a> -->
      </div>
    </body>
    </html>
    <?php
    exit;
  } else {
    throw $e;
  }
}


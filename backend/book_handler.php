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
    exit("That slot is already taken.");
  } else {
    throw $e;
  }
}

<?php
require_once __DIR__ . '/db.php';

$doctorId = intval($_GET['doctor_id'] ?? 0);
$date = $_GET['date'] ?? '';

if (!$doctorId || !$date) {
  echo json_encode([]); exit;
}

$w = (int) date('w', strtotime($date));

$stmt = $pdo->prepare("SELECT start_time, end_time, slot_minutes
                       FROM doctor_schedules WHERE doctor_id=? AND weekday=?");
$stmt->execute([$doctorId, $w]);
$row = $stmt->fetch();
if (!$row) { echo json_encode([]); exit; }

$start = strtotime($row['start_time']);
$end   = strtotime($row['end_time']);
$step  = (int)$row['slot_minutes'];

$occupied = $pdo->prepare("SELECT appt_time FROM appointments
                           WHERE doctor_id=? AND appt_date=? AND status='booked'");
$occupied->execute([$doctorId, $date]);
$busy = array_column($occupied->fetchAll(), 'appt_time');

$slots = [];
for ($t=$start; $t<$end; $t += $step*60) {
  $hhmmss = date('H:i:s', $t);
  if (!in_array($hhmmss, $busy, true)) {
    $slots[] = substr($hhmmss,0,5);
  }
}

header('Content-Type: application/json');
echo json_encode($slots);

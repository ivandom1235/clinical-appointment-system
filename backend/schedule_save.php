
<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_role('doctor');

// Current doctor_id this for doctors page
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id=?");
$stmt->execute([$uid]);
$doctor_id = $stmt->fetchColumn();
if (!$doctor_id) { http_response_code(403); exit('No doctor profile'); }

// Expect arrays for 7 weekdays
$start  = $_POST['start']  ?? []; // e.g., ["", "09:00", "09:00", ...]
$end    = $_POST['end']    ?? [];
$slot   = $_POST['slot']   ?? []; // e.g., ["", "30", "30", ...]
$enable = $_POST['enable'] ?? []; // checkbox "on" for enabled days

// Basic validation helper
function valid_time($t) {
  return (bool)preg_match('/^(2[0-3]|[01]\d):[0-5]\d$/', $t);
}
function valid_slot($s) {
  return ctype_digit($s) && (int)$s > 0 && (int)$s <= 180;
}

// Upsert 0..6
for ($w = 0; $w <= 6; $w++) {
  $is_on = isset($enable[(string)$w]) || isset($enable[$w]);
  $st    = trim($start[$w] ?? '');
  $en    = trim($end[$w]   ?? '');
  $sl    = trim($slot[$w]  ?? '');

  if ($is_on) {
    if (!valid_time($st) || !valid_time($en) || !valid_slot($sl)) {
      continue; // skip invalid row silently
    }
    if (strtotime($st) >= strtotime($en)) {
      continue; // start must be earlier than end
    }

    // Upsert
    $sql = "
      INSERT INTO doctor_schedules (doctor_id, weekday, start_time, end_time, slot_minutes)
      VALUES (?, ?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE start_time=VALUES(start_time), end_time=VALUES(end_time), slot_minutes=VALUES(slot_minutes)
    ";
    $pdo->prepare($sql)->execute([$doctor_id, $w, $st.':00', $en.':00', (int)$sl]);
  } else {
    // If disabled, delete existing row (optional)
    $pdo->prepare("DELETE FROM doctor_schedules WHERE doctor_id=? AND weekday=?")->execute([$doctor_id, $w]);
  }
}

header("Location: /doctor/schedule.php?saved=1");
exit;

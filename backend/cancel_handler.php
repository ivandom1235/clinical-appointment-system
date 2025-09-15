<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_role('patient');

$apptId = intval($_POST['appointment_id'] ?? 0);
$reason = trim($_POST['reason'] ?? 'user_cancel');

if (!$apptId) {
  http_response_code(422);
  exit('Missing appointment_id');
}

/* ensure this appointment belongs to the logged-in patient */
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("
  SELECT a.id, p.user_id AS patient_user_id
  FROM appointments a
  JOIN patients p ON p.id = a.patient_id
  WHERE a.id=? LIMIT 1
");
$stmt->execute([$apptId]);
$row = $stmt->fetch();

if (!$row || (int)$row['patient_user_id'] !== (int)$uid) {
  http_response_code(403);
  exit('Forbidden');
}

$pdo->beginTransaction();
try {
  $pdo->prepare("UPDATE appointments SET status='cancelled' WHERE id=?")->execute([$apptId]);

  // insert into cancellations if the table exists
  try {
    $pdo->prepare("INSERT INTO cancellations (appointment_id, cancelled_by, reason) VALUES (?,?,?)")
        ->execute([$apptId, $uid, $reason]);
  } catch (Throwable $e) {
    // table might not exist; ignore quietly
  }

  $pdo->commit();
  header("Location: /patient/appointments.php?cancelled=1");
  exit;
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(500);
  exit('Cancel failed: ' . htmlspecialchars($e->getMessage()));
}

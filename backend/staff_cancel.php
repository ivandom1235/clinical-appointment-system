<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_role('staff');

$apptId  = intval($_POST['appointment_id'] ?? 0);
$reason  = trim($_POST['reason'] ?? 'staff_cancel');
$redir   = $_POST['redirect_date'] ?? date('Y-m-d');

if (!$apptId) { http_response_code(422); exit('Missing appointment_id'); }

// Make sure appointment exists
$chk = $pdo->prepare("SELECT id FROM appointments WHERE id=? LIMIT 1");
$chk->execute([$apptId]);
if (!$chk->fetch()) { http_response_code(404); exit('Appointment not found'); }

$uid = $_SESSION['uid'];

$pdo->beginTransaction();
try {
  $pdo->prepare("UPDATE appointments SET status='cancelled' WHERE id=?")->execute([$apptId]);

  // log if table exists
  try {
    $pdo->prepare("INSERT INTO cancellations (appointment_id, cancelled_by, reason) VALUES (?,?,?)")
        ->execute([$apptId, $uid, $reason]);
  } catch (Throwable $e) {}

  $pdo->commit();
  header("Location: /staff/appointments.php?date=" . urlencode($redir) . "&cancelled=1");
  exit;
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(500);
  exit('Cancel failed: ' . htmlspecialchars($e->getMessage()));
}

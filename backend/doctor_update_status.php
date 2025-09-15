<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_role('doctor');

$apptId = intval($_POST['appointment_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';

if (!$apptId || !in_array($newStatus, ['completed','no_show'], true)) {
  http_response_code(422);
  exit('Bad request');
}

/* fetch this doctor_id for the logged-in user */
$uid = $_SESSION['uid'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id=?");
$stmt->execute([$uid]);
$doctorId = $stmt->fetchColumn();
if (!$doctorId) { http_response_code(403); exit('No doctor profile'); }

/* verify the appointment belongs to this doctor */
$chk = $pdo->prepare("SELECT id FROM appointments WHERE id=? AND doctor_id=? LIMIT 1");
$chk->execute([$apptId, $doctorId]);
if (!$chk->fetch()) { http_response_code(403); exit('Forbidden'); }

/* update */
$pdo->prepare("UPDATE appointments SET status=? WHERE id=?")->execute([$newStatus, $apptId]);

header("Location: /doctor/dashboard.php?updated=1");
exit;

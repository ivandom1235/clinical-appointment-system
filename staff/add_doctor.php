<?php
$pageCss = '/assets/css/pages/staff_add_doctor.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('staff');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $specialty = trim($_POST['specialty'] ?? '');
    $about     = trim($_POST['about'] ?? '');

    // Basic validation
    if (!$name || !$email || !$password || !$specialty) {
        $error = "All required fields must be filled.";
    } else {
        try {
            // check duplicate email
            $chk = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
            $chk->execute([$email]);
            if ($chk->fetch()) {
                $error = "Email is already in use.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                $pdo->beginTransaction();

                // 1) users row with role=doctor
                $u = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?, 'doctor')");
                $u->execute([$name, $email, $hash]);
                $user_id = (int)$pdo->lastInsertId();

                // 2) doctors row referencing users.id
                $d = $pdo->prepare("INSERT INTO doctors (user_id, specialty, about) VALUES (?,?,?)");
                $d->execute([$user_id, $specialty, $about]);

                $pdo->commit();
                $success = "Doctor created successfully. They can now log in at /auth/login.php";
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = "Failed to create doctor: " . htmlspecialchars($e->getMessage());
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>
<section class="card">
  <h2> Add Doctor</h2>

  <?php if ($success): ?>
    <p style="color:#22c55e;"><?= $success ?></p>
  <?php endif; ?>
  <?php if ($error): ?>
    <p style="color:#ef4444;"><?= $error ?></p>
  <?php endif; ?>

  <form method="post" style="display:grid; gap:10px; max-width:520px;">
    <label>Name* <input type="text" name="name" required></label>
    <label>Email* <input type="email" name="email" required></label>
    <label>Password* <input type="password" name="password" required minlength="6"></label>
    <label>Specialty* <input type="text" name="specialty" placeholder="e.g., General Medicine" required></label>
    <label>About <textarea name="about" rows="3" placeholder="Short bio/clinic details"></textarea></label>
    <div>
      <button type="submit">Create Doctor</button>
      <a href="/staff/dashboard.php" style="margin-left:8px;">Back to Dashboard</a>
    </div>
  </form>
</section>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

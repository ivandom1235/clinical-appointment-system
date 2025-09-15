<?php $pageCss = '/assets/css/pages/auth.css'; ?>
<?php
require_once __DIR__ . '/../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['uid']  = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($user['role'] === 'patient') header("Location: /patient/dashboard.php");
        elseif ($user['role'] === 'doctor') header("Location: /doctor/dashboard.php");
        elseif ($user['role'] === 'staff') header("Location: /staff/dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="card auth-card">
  <h2>Login</h2>
  <?php if (!empty($_GET['ok'])): ?>
    <p class="success">Registration successful. Please login.</p>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button type="submit">Login</button>
  </form>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

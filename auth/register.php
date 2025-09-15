<?php
// tell header which CSS to load
$pageCss = '/assets/css/pages/auth.css';

require_once __DIR__ . '/../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    if ($name && $email && $pass) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?, 'patient')");
            $stmt->execute([$name, $email, $hash]);
            header("Location: login.php?ok=1");
            exit;
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="card auth-card">
  <h2>Patient Registration</h2>
  <?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Name
      <input type="text" name="name" required>
    </label>
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button type="submit">Register</button>
  </form>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

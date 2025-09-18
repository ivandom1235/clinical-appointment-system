<?php require __DIR__ . '/partials/header.php'; ?>

<section class="card" style="max-width:420px; margin:auto; text-align:center;">
  <h2>Welcome to Clinic Booking</h2>
  <p>Please login or register to continue.</p>

  <div style="display:flex; flex-direction:column; gap:12px; margin-top:16px;">
    <a class="btn-primary" href="/auth/login.php"> Login</a>
    <a class="btn-secondary" href="/auth/register.php"> Register as Patient</a>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?> 

<?php

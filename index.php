<?php require __DIR__ . '/partials/header.php'; ?>

<section class="card" style="max-width:420px; margin:auto; text-align:center;">
  <h2>Welcome to Clinic Booking</h2>
  <p>Please login or register to continue.</p>

  <div style="display:flex; flex-direction:column; gap:12px; margin-top:16px;">
    <a class="btn-primary" href="/auth/login.php">🔑 Login</a>
    <a class="btn-secondary" href="/auth/register.php">🧑 Register as Patient</a>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?> 

<?php
/**
 * Documentation: index.php
 *
 * Purpose:
 * This file serves as the main entry point for the web page. It typically includes the header, main content, and footer sections.
 *
 * Concepts & Keywords:
 * - PHP: A server-side scripting language used for web development.
 * - require: A PHP statement that includes and evaluates a specified file. If the file is not found, it produces a fatal error and stops script execution.
 * - __DIR__: A magic constant in PHP that returns the directory of the current file.
 *
 * Line-by-line Explanation:
 * 1. </div>
 *    - Closes a previously opened <div> HTML tag, marking the end of a container or section.
 * 2. </section>
 *    - Closes a previously opened <section> HTML tag, ending a logical section of the page.
 * 3. <?php require __DIR__ . '/partials/footer.php'; ?>
 *    - PHP code that includes the 'footer.php' file from the 'partials' directory located in the same directory as this file.
 *    - This is used to append the footer section to the page.
 *    - The use of __DIR__ ensures the correct path is used regardless of where the script is executed from.
 *
 * Best Practices:
 * - Using require for critical includes ensures the page does not load if a required file is missing.
 * - Organizing reusable sections (like footers) in partials helps maintain clean and modular code.
 */
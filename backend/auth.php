<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function require_login() {
    if (empty($_SESSION['uid'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
}
/*
|--------------------------------------------------------------------------
| auth.php — Authentication & Access Control
|--------------------------------------------------------------------------
|
| This file manages session-based authentication and role-based access control
| for the Clinic Booking System.
|
| Main Features:
| - Starts a PHP session if it’s not already active.
| - Provides the is_logged_in() helper to check login status.
| - Provides require_role($role) to restrict access to users with a specific role.
| - Sets HTTP 403 status and exits for unauthorized access attempts.
|
| Usage Example:
|   require_once __DIR__ . '/../backend/auth.php';
|   require_role('staff'); // Only staff can access this page
|
| Session Data Used:
|   $_SESSION['uid']   — Logged-in user’s ID.
|   $_SESSION['role']  — Role of the user ('patient', 'doctor', 'staff', etc).
|
| Security Note:
| - Sessions store sensitive user info server-side.
| - Only a session ID (PHPSESSID) is sent to the browser as a cookie.
| - Never expose session data in production.
|
| Author: [Your Name]
| Date: [Date]
|
*/

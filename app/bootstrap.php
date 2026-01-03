<?php
// app/bootstrap.php

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define Constants
define('APP_ROOT', dirname(__DIR__));

// Dynamic URL Root (detects http/https and host:port automatically)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('URL_ROOT', $protocol . '://' . $host);

// Error Reporting (Development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require Core Files
require_once __DIR__ . '/model/Database.php';
require_once __DIR__ . '/model/Validator.php';

// Helper Functions
function redirect($url) {
    header("Location: " . URL_ROOT . "/" . $url);
    exit();
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Verify CSRF Token
 */
function verify_csrf() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF validation failed.');
    }
}

/**
 * Generate CSRF Field
 */
function csrf_field() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('app/controller/login.php');
    }
}

/**
 * Require admin
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        die('Access Denied: Admins only.');
    }
}

<?php
require_once __DIR__ . '/db.php';
session_start_safe();

if (auth_check()) {
    redirect(BASE_URL . '/dashboard.php');
} else {
    redirect(BASE_URL . '/login.php');
}

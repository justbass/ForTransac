<?php
require_once 'config.php';
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/kasir.php');
} else {
    header('Location: ' . BASE_URL . '/pages/login.php');
}
exit;

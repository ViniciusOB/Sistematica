<?php
// public/index.php
session_start();
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['papel'] == 'Administrador') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: professor/dashboard.php');
    }
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>

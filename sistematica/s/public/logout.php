<?php
// public/logout.php
session_start();
session_destroy();
header('Location: ./'); // Caminho relativo para subir um nível e acessar login.php
exit;
?>

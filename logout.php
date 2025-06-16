<?php
// logout.php (di root folder 'mengelola_pelanggan_wifi')
session_start();
session_unset();
session_destroy();
header('Location: login.php'); // Path langsung ke login.php karena di root yang sama
exit;
?>
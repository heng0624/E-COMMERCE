<?php
session_start();
session_destroy(); // Clear all session data
header("Location: admin_login.php"); // Redirect to homepage
exit();

?>
<?php
session_start();

// Destroy session
session_destroy();

// Redirect to home
header("Location: index.php");
exit();
?>

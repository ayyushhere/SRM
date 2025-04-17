<?php
// backend/logout.php

session_start();
session_unset();
session_destroy();

// Redirect to login page
header("Location: ../frontend/index.html");
exit();
?>
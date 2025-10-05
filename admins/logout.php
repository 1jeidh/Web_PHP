<?php
session_start();

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset(); 
    session_destroy(); 

    header('Location: admin_login.php');
    exit;
}
?>

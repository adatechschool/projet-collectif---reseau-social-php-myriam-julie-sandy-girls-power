<?php 
session_start();
//print_r($_SESSION);
if (!isset($_SESSION['connected_id'])){
    header("location:login.php");
}
?> 
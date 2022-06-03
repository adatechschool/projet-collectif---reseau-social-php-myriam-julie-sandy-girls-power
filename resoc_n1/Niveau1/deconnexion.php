<?php 
/* if(isset($_SESSION)){
    session_start(); 
    session_destroy();
    session_unset(); 
    header('Location: login.php');
}
exit; */

session_start();
session_unset();
session_destroy();

header("location:login.php");
exit();
?> 
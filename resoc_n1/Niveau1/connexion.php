<?php 

if(!isset($_SESSION)){
    session_start();
    $userId = intval($_SESSION['connected_id']); 
}

?> 
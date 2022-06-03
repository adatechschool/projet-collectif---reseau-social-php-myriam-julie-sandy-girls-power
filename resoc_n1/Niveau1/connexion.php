<?php 

if(!isset($_SESSION)){
    session_start();
    echo session_id();
    $userId = intval($_SESSION['connected_id']); 
}

?> 
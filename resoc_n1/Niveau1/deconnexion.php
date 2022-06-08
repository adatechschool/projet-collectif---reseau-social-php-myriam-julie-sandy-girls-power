<?php 
session_start(); //obligatoire : a mettre en premier dans le code sur chaque fichier php
session_unset(); //vide ton fichier, remet a zero
session_destroy(); //supprime le fichier virtuel
$userId = -1;

header("location:login.php");
exit();
?> 
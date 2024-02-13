<?php
session_start();
require_once 'db_connection.php';

if (isset($_GET["id"])){
    $id=$_GET["id"];

    $sql="DELETE FROM users WHERE user_id=$id";
    $conn->query($sql);
    
} 
header("location:/website/admin.php");
exit;
?>
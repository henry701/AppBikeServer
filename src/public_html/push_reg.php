<?php
require '../includes/core.php';
$con = PDO_MODDED::getInstance();


$query = "INSERT INTO appb_push_regs (id_usuario,regId) VALUES ({$_SESSION['userid']},'{$_POST['regid']}')";
$con->query($query);

<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();

// Select todos os pareamentos
$stmt = $DBInstance->prepare("SELECT tb_pr.id_rastreador as id, tb_pr.habilitado as habilitado, tb_pr.aceito as aceito, (SELECT email FROM appb_usuarios as tb_usr WHERE tb_usr.id = tb_pr.id_rastreador LIMIT 1) as email FROM appb_pareamentos as tb_pr WHERE id_rastreado = :id_usuario;");
$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $stmt, $result);

$Pareamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "";
$ReturnArr['data'] = $Pareamentos;

JsonResponse($ReturnArr);
<?php
require_once '../includes/core.php';

critical_logval();

$ReturnArr = Array();
$DBInstance = PDO_MODDED::getInstance();

$stmt = $DBInstance->prepare("SELECT (habilitado = true AND aceito = true) as permitido FROM appb_pareamentos WHERE id_rastreador = :id_rastreador AND id_rastreado = :id_rastreado LIMIT 1");
$stmt->bindValue(':id_rastreador', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->bindValue(':id_rastreado', $_POST['id'], PDO::PARAM_INT);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $stmt, $result);

$permissao = $stmt->fetchColumn();

if($permissao == FALSE)
{
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Não possui permissão para rastrear essa conta!";
	$ReturnArr['data'] = NULL;
  $ReturnArr['special'] = "NOPERM";
	JsonResponse($ReturnArr);
}

$stmt = $DBInstance->prepare("SELECT latitude as lat, longitude as lng, data_localizacao as date FROM appb_usuarios WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
$result = $stmt->execute();

// Verifica execução da query
IfDBErrorDebug($DBInstance, $stmt, $result);

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "Localização recebida com sucesso!";
$ReturnArr['data'] = $stmt->fetch(PDO::FETCH_ASSOC);

JsonResponse($ReturnArr);

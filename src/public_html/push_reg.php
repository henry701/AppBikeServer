<?php
require_once '../includes/core.php';

critical_logval();

$ReturnArr = Array();

$DBInstance = PDO_MODDED::getInstance();

$stmt = $DBInstance->prepare('SELECT COUNT(id_usuario) FROM appb_push_regs WHERE id_usuario = :id_usuario AND regId = :regId LIMIT 1;');
$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->bindValue(':regId', $_POST['regid'], PDO::PARAM_STR);
$result = $stmt->execute();

if($result === FALSE)
{
	$DBInstance::Debug_PDO_Error($stmt);
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Erro interno do servidor";
	$ReturnArr['data'] = "File: " . __FILE__ . "\nLine: " . __LINE__;
	JsonResponse($ReturnArr);
}

$HasCad = $stmt->fetchColumn();

if($HasCad > 0)
{
	$ReturnArr['result'] = TRUE;
	$ReturnArr['message'] = "Aparelho já estava registrado!";
	JsonResponse($ReturnArr);
}

$stmt = $DBInstance->prepare('INSERT INTO appb_push_regs (id_usuario, regId) VALUES (:id_usuario, :regId);');
$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->bindValue(':regId', $_POST['regid'], PDO::PARAM_STR);
$result = $stmt->execute();

if($result === FALSE)
{
	$DBInstance::Debug_PDO_Error($stmt);
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Erro interno do servidor";
	$ReturnArr['data'] = "File: " . __FILE__ . "\nLine: " . __LINE__;
	JsonResponse($ReturnArr);
}

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "Aparelho registrado com sucesso!";
JsonResponse($ReturnArr);
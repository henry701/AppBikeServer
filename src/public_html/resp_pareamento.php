<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();

$_POST['resp'] = str_case_to_bool($_POST['resp']);
$_POST['id'] = intval($_POST['id']);

if($_POST['resp'] === FALSE)
{
	// Deleta pareamento
	$stmt = $DBInstance->prepare("DELETE FROM appb_pareamentos WHERE id_rastreador = :id_deletar AND id_rastreado = :id_usuario;");
	$stmt->bindValue(':id_deletar', $_POST['id'], PDO::PARAM_INT);
	$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
	$result = $stmt->execute();
	IfDBErrorDebug($DBInstance, $result);
	$rowCount = $stmt->rowCount();
	if($rowcount === 1)
	{
		$ReturnArr['result'] = TRUE;
		$ReturnArr['message'] = "Pareamento recusado com sucesso!";
	}
	else
	{
		$ReturnArr['result'] = FALSE;
		$ReturnArr['message'] = "Erro ao recusar pareamento!";
	}
}
else
{
	// Setta status de pareamento para aceito
	$stmt = $DBInstance->prepare("UPDATE appb_pareamentos SET aceito = TRUE WHERE id_rastreador = :id_aceitar AND id_rastreado = :id_usuario;");
	$stmt->bindValue(':id_aceitar', $_POST['id'], PDO::PARAM_INT);
	$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
	$result = $stmt->execute();
	IfDBErrorDebug($DBInstance, $result);
	$rowCount = $stmt->rowCount();
	if($rowcount === 1)
	{
		$ReturnArr['result'] = TRUE;
		$ReturnArr['message'] = "Pareamento aceito com sucesso!";
	}
	else
	{
		$ReturnArr['result'] = FALSE;
		$ReturnArr['message'] = "Erro ao aceitar pareamento!";
	}
}

JsonResponse($ReturnArr);
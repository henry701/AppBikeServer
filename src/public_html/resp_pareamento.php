<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();

$_POST['resp'] = str_case_to_bool($_POST['resp']);
$_POST['id'] = intval($_POST['id']);
$_POST['had'] = str_case_to_bool($_POST['had']);
$_POST['rastreado'] = str_case_to_bool($_POST['rastreado']);

if($_POST['resp'] === FALSE)
{
	// Deleta pareamento
	$stmt = $DBInstance->prepare("DELETE FROM appb_pareamentos WHERE id_rastreador = :id_rastreador AND id_rastreado = :id_rastreado;");
	if($_POST['rastreado'] === FALSE)
	{
		$stmt->bindValue(':id_rastreador', $_POST['id'], PDO::PARAM_INT);
		$stmt->bindValue(':id_rastreado', $_SESSION['userid'], PDO::PARAM_INT);
	}
	else
	{
		$stmt->bindValue(':id_rastreador', $_SESSION['userid'], PDO::PARAM_INT);
		$stmt->bindValue(':id_rastreado', $_POST['id'], PDO::PARAM_INT);
	}
	$result = $stmt->execute();
	IfDBErrorDebug($DBInstance, $stmt, $result);
	$rowCount = $stmt->rowCount();
	if($rowCount === 1)
	{
		$ReturnArr['result'] = TRUE;
		if($_POST['had'] === FALSE)
			$ReturnArr['message'] = "Pareamento recusado com sucesso!";
		else
			$ReturnArr['message'] = "Pareamento excluído com sucesso!";
	}
	else
	{
		$ReturnArr['result'] = FALSE;
		if($_POST['had'] === FALSE)
			$ReturnArr['message'] = "Erro ao recusar pareamento!";
		else
			$ReturnArr['message'] = "Erro ao excluir pareamento!";
	}
}
else
{
	// Setta status de pareamento para aceito
	$stmt = $DBInstance->prepare("UPDATE appb_pareamentos SET aceito = TRUE WHERE id_rastreador = :id_rastreador AND id_rastreado = :id_rastreado;");
	$stmt->bindValue(':id_rastreador', $_POST['id'], PDO::PARAM_INT);
	$stmt->bindValue(':id_rastreado', $_SESSION['userid'], PDO::PARAM_INT);
	$result = $stmt->execute();
	IfDBErrorDebug($DBInstance, $stmt, $result);
	$rowCount = $stmt->rowCount();
	if($rowCount === 1)
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
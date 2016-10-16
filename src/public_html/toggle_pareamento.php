<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();

$_POST['id'] = intval($_POST['id']);
$_POST['state'] = str_case_to_bool($_POST['state']);

// Setta status de pareamento para aceito
$stmt = $DBInstance->prepare("UPDATE appb_pareamentos SET habilitado = :habilitado WHERE id_rastreador = :id_rastreador AND id_rastreado = :id_rastreado;");
$stmt->bindValue(':habilitado', $_POST['state'], PDO::PARAM_BOOL);
$stmt->bindValue(':id_rastreador', $_POST['id'], PDO::PARAM_INT);
$stmt->bindValue(':id_rastreado', $_SESSION['userid'], PDO::PARAM_INT);
$result = $stmt->execute();
IfDBErrorDebug($DBInstance, $stmt, $result);

$rowCount = $stmt->rowCount();
if($rowCount === 1)
{
	$ReturnArr['result'] = TRUE;
	if($_POST['state'] === TRUE)
		$ReturnArr['message'] = "Pareamento habilitado com sucesso!";
	else
		$ReturnArr['message'] = "Pareamento desabilitado com sucesso!";
}
else
{
	$ReturnArr['result'] = FALSE;
	if($_POST['state'] === TRUE)
		$ReturnArr['message'] = "Erro ao habilitar pareamento!";
	else
		$ReturnArr['message'] = "Erro ao desabilitar pareamento!";
}

JsonResponse($ReturnArr);
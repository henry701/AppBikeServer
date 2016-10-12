<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();
$stmt = $DBInstance->prepare("SELECT id FROM appb_usuarios WHERE email = :email LIMIT 1");
$stmt->bindValue(':email', $_POST['email_destino'], PDO::PARAM_STR);
$result = $stmt->execute();

if($result === FALSE)
{
	$DBInstance::Debug_PDO_Error($stmt);
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Erro interno do servidor";
	$ReturnArr['data'] = "File: " . __FILE__ . "\nLine: " . __LINE__;
	JsonResponse($ReturnArr);
}

$idAlvo = $stmt->fetch(PDO::FETCH_ASSOC);

if($idAlvo === FALSE)
{
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = 'Email não localizado!';
	return JsonResponse($ReturnArr);
}

$idAlvo = $idAlvo['id'];

$stmt = $DBInstance->prepare('SELECT regId FROM appb_push_regs WHERE id_usuario = :id_usuario;');
$stmt->bindValue(':id_usuario', $idAlvo, PDO::PARAM_INT);
$result = $stmt->execute();

if($result === FALSE)
{
	$DBInstance::Debug_PDO_Error($stmt);
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Erro interno do servidor";
	$ReturnArr['data'] = "File: " . __FILE__ . "\nLine: " . __LINE__;
	JsonResponse($ReturnArr);
}

$regIdFetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
$regIds = Array();
foreach($regIdFetch as $rows)
{
	array_push($regIds, $rows['regId']);
}

$pusher = new AndroidPusher(GCM_KEY);
$pusher->notify($regIds, "Deseja adicionar USR_TAL?");

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "Envido push para: " . print_r($regIds, TRUE) . "\n\n" . print_r($pusher->getOutputAsArray(), TRUE);

JsonResponse($ReturnArr);
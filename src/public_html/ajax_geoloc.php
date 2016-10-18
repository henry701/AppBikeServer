<?php
require_once '../includes/core.php';

critical_logval();

$ReturnArr = Array();
$DBInstance = PDO_MODDED::getInstance();

$stmt = $DBInstance->prepare("UPDATE appb_usuarios SET latitude = :latitude, longitude = :longitude, data_localizacao = NOW() WHERE id = :id LIMIT 1;");
$stmt->bindValue(':latitude', $_POST['lat'], PDO::PARAM_INT);
$stmt->bindValue(':longitude', $_POST['lng'], PDO::PARAM_INT);
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$result = $stmt->execute();

// Verifica execução da query
IfDBErrorDebug($DBInstance, $stmt, $result);

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "Localização atualizada com sucesso!";

JsonResponse($ReturnArr);

<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();

// Select todos os pareamentos
$stmt = $DBInstance->prepare("
SELECT
	tbl.id_cara as id,
    tbl.habilitado as habilitado,
    tbl.aceito as aceito,
    tbl.rastreado as rastreado,
    tbl.user as user
FROM
(
	(
		SELECT
			IF(tb_p.id_rastreado=:id_usuario, tb_p.id_rastreador, tb_p.id_rastreado) as id_cara,
			tb_p.habilitado as habilitado,
			tb_p.aceito as aceito,
			(tb_p.id_rastreado <> :id_usuario) as rastreado,
			(SELECT tb_u.user FROM appb_usuarios as tb_u WHERE tb_u.id = id_cara LIMIT 1) as user
		FROM
			appb_pareamentos as tb_p
		WHERE
			id_rastreado = :id_usuario OR id_rastreador = :id_usuario
	) tbl
)
;
");
$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
$result = $stmt->execute();

// "rastreado" referente ao usuario da lista, nao o que fez a request

IfDBErrorDebug($DBInstance, $stmt, $result);

$Pareamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "";
$ReturnArr['data'] = $Pareamentos;

JsonResponse($ReturnArr);

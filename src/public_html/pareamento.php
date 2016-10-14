<?php
require_once '../includes/core.php';

critical_logval();

$DBInstance = PDO_MODDED::getInstance();

$ReturnArr = Array();

// Pegar ID do alvo pelo email
$stmt = $DBInstance->prepare("SELECT id FROM appb_usuarios WHERE email = :email LIMIT 1;");
$stmt->bindValue(':email', $_POST['email_destino'], PDO::PARAM_STR);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $result);

$idAlvo = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar id do alvo
if($idAlvo === FALSE)
{
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = 'Email não localizado!';
	JsonResponse($ReturnArr);
}
$idAlvo = $idAlvo['id'];
if($idAlvo == $_SESSION['userid'])
{
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = 'Este e-mail pertence a sua própria conta!';
	JsonResponse($ReturnArr);
}



// Checar se já tem esse pareamento
$stmt = $DBInstance->prepare('SELECT aceito FROM appb_pareamentos WHERE id_rastreador = :id_rastreador AND id_rastreado = :id_rastreado;');
$stmt->bindValue(':id_rastreador', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->bindValue(':id_rastreado', $idAlvo, PDO::PARAM_INT);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $result);

$AlreadyPareado = $stmt->fetch(PDO::FETCH_ASSOC);

if($AlreadyPareado !== FALSE)
{
	$AlreadyPareado = boolval($AlreadyPareado['aceito']);
	if($AlreadyPareado === TRUE)
	{
		$ReturnArr['result'] = FALSE;
		$ReturnArr['message'] = 'Esta conta já está pareada!';
	}
	else
	{
		$ReturnArr['result'] = FALSE;
		$ReturnArr['message'] = 'Já existe um pedido de pareamento pendente com essa conta!';
	}
	JsonResponse($ReturnArr);
}




// Inserir pareamento pendente na tabela
$stmt = $DBInstance->prepare('INSERT INTO appb_pareamentos (id_rastreador, id_rastreado, habilitado, aceito) VALUES (:id_rastreador, :id_rastreado, FALSE, FALSE);');
$stmt->bindValue(':id_rastreador', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->bindValue(':id_rastreado', $idAlvo, PDO::PARAM_INT);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $result);



// Pegar nome do usuário que quer o pareamento
$stmt = $DBInstance->prepare('SELECT email, nome FROM appb_usuarios WHERE id = :id_usuario;');
$stmt->bindValue(':id_usuario', $_SESSION['userid'], PDO::PARAM_INT);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $result);

$NomeRequest = $stmt->fetch(PDO::FETCH_ASSOC);
$NomeRequest = $NomeRequest['nome'];


// Enviar Push
$stmt = $DBInstance->prepare('SELECT regId FROM appb_push_regs WHERE id_usuario = :id_usuario;');
$stmt->bindValue(':id_usuario', $idAlvo, PDO::PARAM_INT);
$result = $stmt->execute();

IfDBErrorDebug($DBInstance, $result);




$regIdFetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
$regIds = Array();
foreach($regIdFetch as $rows)
{
	array_push($regIds, $rows['regId']);
}

$pusher = new AndroidPusher(GCM_KEY);
//$pusher->notify($regIds, "$NomeRequest deseja parear com a sua conta!");

$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = "Enviado push para: " . print_r($regIds, TRUE) . "\n\n" . print_r($pusher->getOutputAsArray(), TRUE);

JsonResponse($ReturnArr);

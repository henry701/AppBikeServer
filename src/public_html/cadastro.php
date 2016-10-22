<?php
require_once '../includes/core.php';

$ReturnArr = Array();

/*
$_POST['user']
$_POST['senha']
$_POST['nome']
*/

// Validar e-mail, nome e senha (senha deve ser menor do que 72 caracteres (limitação do Blowfish), e maior do que 4 caracteres)
if(strlen($_POST['user']) < 4)
{
	$ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Nome de Usuário inválido! (Mínimo de 4 caracteres)";
	JsonResponse($ReturnArr);
}
if(strlen($_POST['senha']) < 4)
{
    $ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "A senha deve possuir um mínimo de 4 caracteres!";
	JsonResponse($ReturnArr);
}
if(strlen($_POST['senha']) >= 72)
{
    $ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "A senha deve possuir no máximo 71 caracteres!";
	JsonResponse($ReturnArr);
}

if(logval(FALSE) === TRUE)
{
	$ReturnArr['result'] = TRUE;
	$ReturnArr['message'] = "Já está logado!";
}
else
{
	$DBInstance = PDO_MODDED::getInstance();
	$stmt = $DBInstance->prepare("SELECT user FROM appb_usuarios WHERE user = :user LIMIT 1");
	$stmt->bindValue(':user', $_POST['user'], PDO::PARAM_STR);
	$result = $stmt->execute();
	if($result === FALSE)
	{
		IfDBErrorDebug($DBInstance, $stmt, $result);
	}
	else
	{
		$j = $stmt->fetch(PDO::FETCH_ASSOC);

		if($j !== FALSE)
		{
			$ReturnArr['result'] = FALSE;
			$ReturnArr['message'] = "Este nome de usuário já existe em nosso sistema!";
			JsonResponse($ReturnArr);
		}
		else
		{
			$Password = CryptBlowFish($_POST['senha'], 10);
			$Password = $Password['cripto'];
			$stmt = $DBInstance->prepare("INSERT INTO appb_usuarios (user, senha) VALUES (:user, :senha)");
			$stmt->bindValue(':senha', $Password, PDO::PARAM_STR);
			$stmt->bindValue(':user', $_POST['user'], PDO::PARAM_STR);
			$result = $stmt->execute();

			IfDBErrorDebug($DBInstance, $stmt, $result);

			$ReturnArr['result'] = TRUE;
			$ReturnArr['message'] = "Cadastro realizado com sucesso!";
		}
	}
}

JsonResponse($ReturnArr);

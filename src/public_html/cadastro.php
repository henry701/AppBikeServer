<?php
require_once '../includes/core.php';

$ReturnArr = Array();

/*
$_POST['email']
$_POST['senha']
$_POST['nome']
*/

// Validar e-mail, nome e senha (senha deve ser menor do que 72 caracteres (limitação do Blowfish), e maior do que 4 caracteres)
if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === FALSE)
{
    $ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "E-mail inválido!";
	JsonResponse($ReturnArr);
}
if(empty($_POST['nome']) === TRUE)
{
    $ReturnArr['result'] = FALSE;
	$ReturnArr['message'] = "Nome inválido!";
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
	$stmt = $DBInstance->prepare("SELECT nome, email FROM appb_usuarios WHERE email = :email OR nome = :nome LIMIT 1");
	$stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
	$stmt->bindValue(':nome', $_POST['nome'], PDO::PARAM_STR);
	$result = $stmt->execute();
	if($result === FALSE)
	{
		$DBInstance::Debug_PDO_Error($stmt, TRUE);
	}
	else
	{
		$j = $stmt->fetch(PDO::FETCH_ASSOC);

		if($j !== FALSE)
		{
			$ReturnArr['result'] = FALSE;
			$ReturnArr['message'] = "Este e-mail ou nome já existe em nosso sistema!";
			JsonResponse($ReturnArr);
		}
		else
		{
			$Password = CryptBlowFish($_POST['senha'], 10);
			$Password = $Password['cripto'];
			$stmt = $DBInstance->prepare("INSERT INTO appb_usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
			$stmt->bindValue(':senha', $Password, PDO::PARAM_STR);
			$stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
			$stmt->bindValue(':nome', $_POST['nome'], PDO::PARAM_STR);
			$result = $stmt->execute();
			if($result === FALSE)
			{
				$DBInstance::Debug_PDO_Error($stmt, TRUE);
			}
			else
			{
				$ReturnArr['result'] = TRUE;
				$ReturnArr['message'] = "Cadastro realizado com sucesso! Cheque seu e-mail para o link de confirmação.";
			}
		}
	}
}

JsonResponse($ReturnArr);
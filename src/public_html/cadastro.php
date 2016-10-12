<?php
require_once '../includes/core.php';

$ReturnArr = Array();

/*
$_POST['email']
$_POST['senha']
$_POST['nome']
*/

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
			// TODO: Validar e-mail, nome e senha (senha deve ser menor do que 72 caracteres, e maior do que 6 caracteres)
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

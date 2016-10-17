	<?php
require_once '../includes/core.php';

$ReturnArr = Array();

if(logval(FALSE) === TRUE)
{
	$ReturnArr['result'] = TRUE;
	$ReturnArr['message'] = "Já estava logado!";
}
else
{
	$DBInstance = PDO_MODDED::getInstance();

	// Contra bruteforce
	sleep(1);

	$stmt = $DBInstance->prepare("SELECT id, senha FROM appb_usuarios WHERE email = :email LIMIT 1");
	$stmt->bindValue(':email', $_POST['user'], PDO::PARAM_STR);
	$result = $stmt->execute();

	if($result === FALSE)
	{
		IfDBErrorDebug($DBInstance, $stmt, $result);
	}
	else
	{
		$j = $stmt->fetch(PDO::FETCH_ASSOC);

		if($j === FALSE)
		{
			$ReturnArr['result'] = FALSE;
			$ReturnArr['message'] = "Usuário não existente!";
		}
		else
		{
			$PasswordCheck = CheckCripto($_POST['senha'], 10, $j['senha'], $j['senha']);

			if($PasswordCheck === TRUE)
			{
				$_SESSION["loggedin"] = TRUE;
				$_SESSION["userid"] = $j['id'];
				$_SESSION["passconf"] = FALSE;
				$_SESSION["last_activity"] = time();
				session_write_close();
				$ReturnArr['result'] = TRUE;
				$ReturnArr['message'] = "Logado com sucesso!";
			}
			else
			{
				$ReturnArr['result'] = FALSE;
				$ReturnArr['message'] = "Senha incorreta!";
			}
		}
	}
}

JsonResponse($ReturnArr);
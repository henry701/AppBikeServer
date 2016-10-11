<?php
exit();
require_once '../includes/core.php';

/* PRA SENHA E LOGIN */
	$Senha = 'apps';
	$Crip = CryptBlowFish($Senha, 10); // Gerar só com a senha, salt random
	$ReCrip = CryptBlowFish($Senha, 10, $Crip['cripto']); // Outra salt (no caso a mesma, mas pode ser outra tb)
	var_dump(CheckCripto($Senha, 10, '', $ReCrip['cripto'])); // TRUE, e NÃO PRECISA DO SALT (nao necessariamente)
	var_dump($ReCrip);

?>
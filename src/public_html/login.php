<?php
require_once '../includes/core.php';

$ReturnArr = Array();

$Senha = 'apps';
$Crip = CryptBlowFish($Senha,10); // Gerar só com a senha, salt random
$ReCrip = CryptBlowFish($Senha,10,$Crip['salt']); // Outra salt (no caso a mesma, mas pode ser outra tb)
var_dump(CheckCripto($Senha, 10, ''/*$Crip['salt']*/, $ReCrip['cripto'])); // TRUE, e NÃO PRECISA DO SALT (nao necessariamente)
var_dump($ReCrip);

exit(json_encode($ReturnArr));
?>
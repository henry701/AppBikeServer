<?php

ini_set('display_errors',1);

require_once '../includes/core.php';

$ReturnArr = Array();
$DBInstance = PDO_MODDED::getInstance();

$stmt = $DBInstance->prepare("SELECT latitude,longitude FROM appb_usuarios WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', 1, PDO::PARAM_STR);
$result = $stmt->execute();

//verifica execução da query
if($result === FALSE){

  $ReturnArr['result'] = FALSE;
  $ReturnArr['message'] = 'erro na query';

}else{
    // $j = $stmt->fetch(PDO::FETCH_ASSOC);
    // print_r($j);
    // die();
    // caso não tenha lat/long devemos receber dados da main-mapas.html e realizar inserção
    // if($j['latitude'] === NULL or $j['longitude'] === NULL)

    $stmt = $DBInstance->query("UPDATE appb_usuarios SET latitude = {$_POST['lat']} , longitude = {$_POST['long']} WHERE id = {$_POST['id']} ;");
    // $stmt = $DBInstance->query("UPDATE appb_usuarios SET latitude = 34 , longitude = 34 WHERE id = 1 ;");
    var_dump($stmt);
    $ReturnArr['result'] = true;
    $ReturnArr['message'] = 'Aparelho localizado';
}
JsonResponse($ReturnArr);

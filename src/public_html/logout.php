<?php
require '../includes/core.php';
$returnArr = array();

if($_POST['logout'] == '') {
  ACTION_logout();
  $returnArr['result'] = TRUE;
  $returnArr['message'] = 'Até logo';
  JsonResponse($returnArr);
}

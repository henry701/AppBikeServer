<?php
require_once '../includes/core.php';

$ReturnArr = Array();

@ACTION_logout();
$ReturnArr['result'] = TRUE;
$ReturnArr['message'] = NULL;
JsonResponse($ReturnArr);
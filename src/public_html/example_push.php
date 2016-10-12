<?php

// https://code.google.com/apis/console/
$apiKey = "AIzaSyD2ZmFDACwQxYtU8H_FpnfQh9oJakrUnIk";
$regId = "faz o select da TB ai vai mesmo nao estando funcionando ainda";

$pusher = new AndroidPusher($apiKey);
$pusher->notify($regId, "Hola");

print_r($pusher->getOutputAsArray());
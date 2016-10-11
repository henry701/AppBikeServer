<?php

ini_set("display_errors",1);

require '../../src/includes/functions.php';

$newClientData = array(
                  "email"=>"'{$_POST['email']}'",
                  "senha"=>"'{$_POST['senha']}'",
                  "nome"=>"'{$_POST['nome']}'"
                );

insertNewUsers($newClientData);


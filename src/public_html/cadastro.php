<?php
ini_set("display_errors",1);

require '../../src/includes/functions.php';

// $array = array(
//                   "senha"=>$_POST['email'],
//                   "nome"=>$_POST['senha']
//                   "email"=>$_POST['nome'],
//                 );

$newClientData = array(
                  "email"=>"'eaaaa@hotmail.com '",
                  "senha"=>"'apps'",
                  "nome"=>"'Victor Zampieri'"
                );

insertNewUsers($newClientData);

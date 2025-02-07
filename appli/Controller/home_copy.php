<?php
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//require_once 'init.php';
// echo 'test accès';

if($_GET['action'] == 'readOne' ) // une autre maniere d'inserer de traiter l'insertion
{
    $data1 = [1=>"hello"];
  
   

    echo json_encode($data1); // pour la reponse on encode en json

}

$title = 'Florence Illiano - Accueil';


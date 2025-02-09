<?php
declare (strict_types=1);

header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "config/config.php"; 
spl_autoload_register(function($class) {
    $directories = ['Entity','Controller','Repository', 'kernel','ErrorHandler'];
    
    foreach($directories as $directory){
        $file = __DIR__ . "/appli/$directory/$class.php";
        if(file_exists($file)){
            require_once $file ;
        }
    }
});

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

set_error_handler('ErrorHandler::handleError'); // mettre en place la gestion des erreurs, les transformer en exceptions
set_exception_handler('ErrorHandler::handleException'); // mettre en place la gestion des exceptions

header('Content-Type: application/json');

$parts =  explode("/", $_SERVER["REQUEST_URI"]);

// print_r($parts); // **** a enlever

if ( !in_array( $parts[3], COLLECTIONS) ) { // si pas dans le tableau des collections
    http_response_code(404);
    exit;
}


$database = new Database('localhost', DB_BASE, DB_USER, DB_PASS);
$database->getConnection();

$table = convertToPascalCase(substr( $parts[3],0,-1 ));
if ($table == 'LessonCategorie') { // cas particulier
    $table = 'LessonCategory';
}
var_dump($table);
$itemRepository = $table .'Repository';
$Repository = new $itemRepository($database);


$id = isset($parts[4]) ? (int)$parts[4] : null;
$itemController = $table.'Controller'; // string
$Controller = new $itemController($Repository) ;  // todo automatiser en utilisant $parts[3]

$Controller->processRequest($_SERVER["REQUEST_METHOD"], $id);


function convertToPascalCase(string $url): string
{
   return str_replace('-', '', ucwords($url, '-'));
}


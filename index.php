<?php


require_once("./vendor/autoload.php");
require_once("./vendor/altorouter/altorouter/AltoRouter.php");
require_once('./model/Crud.php');


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$router = new AltoRouter();

$router->setBasePath("/api_pokedex");

$router->map("GET","/api/pokemon/all",function(){

    $db = new crud($_ENV['DATABASE'],$_ENV['NDC'],$_ENV['MDP']);
    $allPokemon = json_encode($db->getAll());
    header('Content-Type: application/json');
    echo $allPokemon;

    
},"route_all");

//faire la route api/pokemon/name/[name]

$router->map("GET","/api/pokemon/name/[a:name]",function($name){

    $name = htmlspecialchars($name);
    $db = new crud($_ENV['DATABASE'],$_ENV['NDC'],$_ENV['MDP']);
    $data = json_encode($db->getOneByName($name));
    header('Content-Type: application/json');
    echo $data;

    
    
},"route_one");

//api/pokemon/type/[type]


$router->map("GET","/api/pokemon/type/[*:type]",function($type){

    $type = htmlspecialchars($type);
    $db = new crud($_ENV['DATABASE'],$_ENV['NDC'],$_ENV['MDP']);
    $tablo = explode(',',$type);
    $typePokemon = $db->getByMultiType($tablo);
    if(empty($typePokemon)){
        $data = 'aucun resultat';
    }else{
        $data = $typePokemon;
    }
    $data = json_encode($data);
    header('Content-Type: application/json');
    echo $data;
    
},"route_type");

//api/pokemon/faiblesse/[faiblesse]

$router->map("GET","/api/pokemon/faiblesse/[*:faiblesse]",function($faiblesse){

    $faiblesse = htmlspecialchars($faiblesse);
    $db = new crud($_ENV['DATABASE'],$_ENV['NDC'],$_ENV['MDP']);
    $tablo = explode(',',$faiblesse);
    $weakPokemon = $db->getByMultiWeak($tablo);
    if(empty($weakPokemon)){
        $data = 'aucun resultat';
    }else {
        $data = $weakPokemon;
    }
    $data = json_encode($data);
    header('Content-Type: application/json');
    echo $data;
   
},"route_faiblesse");


$match =  $router->match();

if( is_array($match) && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] ); 
} else {
   
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

?>
<?php 

session_start();

if(!isset($_SESSION["Users"])){
      $_SESSION["Users"] = [["id" => "1", 'name' => "Paul"], ["id" => "2", 'name' => "John"], ["id" => "3", 'name' => "Dale"], ["id" => "4", 'name' => "Fred"]];
      echo "her";
      
 }

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_REQUEST['request'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$model = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;
 
switch ($method) {
  case 'GET':
    if($key > 0){
        foreach($_SESSION["Users"] as $person) {
            if ($key == $person['id']) {
                $result = array('success' => true, 'message' => $person); 
                break;
            }
        }
    }else{
        $result = array('success' => true, 'message' => $_SESSION["Users"]);
    }
    break;
  case 'PUT':
   foreach($_SESSION["Users"] as $person) {
        if ($key == $person['id']) {
            $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
            $values = array_map(function ($value){
                if ($value===null) return null;
                return $value;
            },array_values($input));
            for ($i=0;$i<count($columns);$i++) {
                $_SESSION["Users"][$person['id']][$columns[$i]] = $values[$i];
                }
            
            $result = array('success' => true, 'message' =>  "Done");
        }
    } break;
  case 'POST':
    // Add Item to session
    $length = sizeof( $_SESSION["Users"] );
    
    $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
    $values = array_map(function ($value){
        if ($value===null) return null;
        return $value;
    },array_values($input));
    $tmp = [];
    $tmp['id'] = $length+1;
        
    for ($i=0;$i<count($columns);$i++) {
        $tmp[$columns[$i]] = $values[$i];
    }
    array_push($_SESSION["Users"], $tmp );
    $result = array('success' => true, 'message' =>  "Done");
    break;
  case 'DELETE':
    for ($i=0;$i<count($_SESSION["Users"]);$i++) {
        if ($key == $_SESSION["Users"][$i]['id']) {
            unset($_SESSION["Users"][$i]);
            $result = array('success' => true, 'message' =>  "Done"); break;
        }
    } 
   break;
}
echo json_encode($result);
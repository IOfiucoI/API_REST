<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// Libreria JWT
include_once '_JWT_Core.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';
include_once '_JWT.php';
use \Firebase\JWT\JWT;
 
// Decodifica objeto
$data = json_decode(file_get_contents("php://input"));
 
// Obtiene token
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// Solicita token
    if($jwt){
    
        try { // Validación de token
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            http_response_code(200);
            echo json_encode(array( "status" => "Token correcto.", "data" => $decoded->data ));
    
        }
    
        catch (Exception $e){
            // Mensaje de error si el token no es válido
            http_response_code(401);
            echo json_encode(array( "status" => "Token inválido.", "error" => $e->getMessage()
            ));
        }
    }
    
    else{
        // Si no se inicia sesión con token enviara mensaje
        http_response_code(401);
        echo json_encode(array("status" => "Acceso denegado."));
    }
?>
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/usuariosModel.php';

// Se inclye la libreria JSON WEB TOKEN
include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$user = new users($db);
$data = json_decode(file_get_contents("php://input"));

/*

Usuario para iniciar sesión:

"correo":"mark_rg@msn.com",
"pass":"123456"

*/

if (
    !empty($data->correo) &&
    !empty($data->pass)
) {

    $user->correo = $data->correo;
    $user->pass = $data->pass;
    $email_exists = $user->emailExists();

    //Se verifica si el email y la contraseña son correctos
    if ($email_exists && password_verify($data->pass, $user->pass)) {

        $token = array(
            "iat"   => $issued_at,
            "exp"   => $expiration_time,
            "iss"   => $issuer,
            "data"  => array(
                "id_usuario"        => $user->id_usuario,
                "correo"            => $user->correo
            )
        );
        // Genera token
        http_response_code(200);
        $jwt = JWT::encode($token, $key);
        echo json_encode(
            array(
                "status"    => "Usuario validado correctamente.",
                "correo"    => $user->correo,
                "jwt"       => $jwt
            )
        );
    } else {
        http_response_code(401);
        echo json_encode(array("status" => "Verifique su usuario y/o contraseña."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => "Error al iniciar sesion."));
}

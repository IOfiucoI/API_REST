<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Se incluye la conexión a la DB y la clase usuarios
include_once '../models/db.php';
include_once '../models/usuariosModel.php';

// Se inclye la libreria JSON WEB TOKEN
include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

//Se inician los objetos de conexión y la clase de usuarios
$database = new DB();
$db = $database->connect();
$user = new users($db);

// $data codifica los datos de JSON a php
$data = json_decode(file_get_contents("php://input"));

/* 
 * REQUEST_METHOD verifica que método se va a utilizar para realizar las peticiones al servidor
 * POST:   Se utiliza para crear datos
 * PUT:    Se utiliza actualizar registros
 * GET:    Obtiene registros
 * DELETE: Elimina registros
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* Se ingresan los valores para crear un nuevo usuario
        
         RUTA POSTMAN: http://localhost/controlles/usuario
         METODO DE ENVIO: POST
         TIPO DE DATOS DE INGRESO RAW
         EJEMPLO PARA CREAR NUEVO USUARIO:
               {
                      "id_institucion":"1",
                      "correo":"email@ejemplo.com",
                      "pass":"contraseña"
                  }
*/

    if (
        !empty($data->id_institucion) &&
        !empty($data->correo) &&
        !empty($data->pass)
    ) {

        $user->id_institucion   = $data->id_institucion;
        $user->correo           = $data->correo;
        $user->pass             = $data->pass;

        if ($email_exists = $user->emailExists()) {
            http_response_code(400);
            echo json_encode(array("status" => "No se puede crear el usuario, el correo ya se encuentra registrado."));
        } else {

            if ($user->createUser()) {
                http_response_code(200);
                echo json_encode(array("status" => "El usuario se registro correctamente."));
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Error al registrar usuario."));
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "Debe ingresar todos los datos para crear un usuario"));
    }
}

    /*  Para editar, eliminar o mostrar registros de usuario se debe tener una sesión activa
        cuando se inicia sesión se muestra los datos: correo y token

        RUTA POSTMAN: localhost/controlles/usuario.php
        METODO DE ENVIO: PUT (EDITAR USUARIO)
        SE DEBE INICIAR CON TOKEN 
        EJEMPLO DE DATOS A INGRESAR:
        {   
            "jwt":"token_de_sesion",
            "id_institución":"nombre",
            "correo":"nuevo correo o el mismo email@ejemplo.com";
            "pass":"nueva contraseña";
        }
    */

if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    $jwt = isset($data->jwt) ? $data->jwt : "";

    if ($jwt) {

        try {

            $decoded = JWT::decode($jwt, $key, array('HS256'));

            if (
                !empty($data->id_usuario) &&
                !empty($data->id_institucion) &&
                !empty($data->correo) &&
                !empty($data->pass)
            ) {

                $user->id_usuario = $data->id_usuario;
                $user->id_institucion = $data->id_institucion;
                $user->correo = $data->correo;
                $user->pass = $data->pass;

                // Inicia función de actualización
                if ($user->updateUser()) {
                    // Obtiene los datos actualizados para generar un nuevo token
                    $token = array(
                        "iat" => $issued_at,
                        "exp" => $expiration_time,
                        "iss" => $issuer,
                        "data" => array(
                            "id_usuario" => $user->id_usuario,
                            "id_institucion" => $user->id_institucion,
                            "correo" => $user->correo,
                            "pass" => $user->pass
                        )
                    );
                    //Genera nuevo token 
                    $jwt = JWT::encode($token, $key);
                    http_response_code(200);
                    echo json_encode(array("status" => "El registro se actualizo correctamente.", "jwt" => $jwt));
                } else {
                    http_response_code(401);
                    echo json_encode(array("status" => "Error al actualizar los datos del usuario."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Debe ingresar todos los datos para actualizar el usuario"));
            }
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(array("status" => "El token no es válido.", "data" => $e->getMessage()));
        }
    } else {
        http_response_code(403);
        echo json_encode(array("status" => "Error: Debe tener una sesión activa."));
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $jwt = isset($data->jwt) ? $data->jwt : "";

    if ($jwt) {

        try {

            $decoded = JWT::decode($jwt, $key, array('HS256'));

            if (isset($_GET['id'])) {

                $user->id_usuario = isset($_GET['id']) ? $_GET['id'] : die();
                $user->userShowOne();

                if ($user->nombre_institucion != null) {
                    // create array
                    $user_arr = array(
                        "id_usuario"            => $user->id_usuario,
                        "id_institucion"        => $user->id_institucion,
                        "nombre_institucion"    => $user->nombre_institucion,
                        "correo"                => $user->correo
                    );
                    http_response_code(200);
                    echo json_encode($user_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No existe el usuario."));
                }
            } else {

                $stmt = $user->userShow();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $usuario = array();
                    $usuario["usuarios"] = array();
                    $usuario["Usuarios registrados"] = $registro;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_usuario"        => $id_usuario,
                            "id_institucion"    => $id_institucion,
                            "correo"            => $correo
                        );

                        array_push($usuario["usuarios"], $e);
                    }
                    http_response_code(200);
                    echo json_encode($usuario);
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(array("status" => "El token no es válido.", "data" => $e->getMessage()));
        }
    } else {
        http_response_code(403);
        echo json_encode(array("status" => "Error: Debe tener una sesión activa."));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    $jwt = isset($data->jwt) ? $data->jwt : "";

    if ($jwt) {

        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));

            if (!empty($data->id_usuario)) {

                $user->id_usuario = $data->id_usuario;

                if (!empty($user->id_usuario) && $user->userDelete()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El usuario se elimino correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error: No se puede eliminar el usuario."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Debe igresar el ID para eliminar el usuario"));
            }
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(array("status" => "El token no es válido.", "data" => $e->getMessage()));
        }
    } else {
        http_response_code(403);
        echo json_encode(array("status" => "Error: Debe tener una sesión activa."));
    }
}

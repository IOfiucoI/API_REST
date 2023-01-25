<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/becasModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$becas = new becas($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) { // INICIA VERIFICACION DE TOKEN

    try {
        // $decoded verifica si el token es válido
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        //INCIAN PETICIONES REQUEST_METHOD: POST, GET, PUT, DELETE

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            /* Ejemplo para crear beca
                {
                    "jwt":" token ",
                    "id_institucion":"1",
                    "programa":"Jóvenes Construyendo el Futuro",
                    "descripcion":"Un programa que vincula a personas de entre 18 y 29 años de edad, que no estudian y no trabajan.",
                    "periodo":"Semestral",
                    "documento":"documento",
                    "institucionesid_institucion":"1"
                } 
            */

            if (
                !empty($data->id_institucion) &&
                !empty($data->programa) &&
                !empty($data->descripcion) &&
                !empty($data->periodo) &&
                !empty($data->documento) &&
                !empty($data->institucionesid_institucion)
            ) {

                $becas->id_institucion = $data->id_institucion;
                $becas->programa = $data->programa;
                $becas->descripcion = $data->descripcion;
                $becas->periodo = $data->periodo;
                $becas->documento = $data->documento;
                $becas->institucionesid_institucion = $data->institucionesid_institucion;

                if ($becas->createBeca()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La beca se registro correctamente."));
                } else {
                    // Mensaje de error si no se puede realizar el registro
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al registrar la beca."));
                }
            } else {
                // Mensaje de datos incompletos
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede crear la beca."));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "PUT") {

            /* Ejemplo para actualizar beca
                {
                    "jwt":" token ",
                    "id_apoyos":"4", <---- ID que sea desea actualizar
                    "id_institucion":"1",
                    "programa":"Jóvenes Construyendo el Futuro",
                    "descripcion":"Un programa que vincula a personas de entre 18 y 29 años de edad, que no estudian y no trabajan.",
                    "periodo":"Semestral",
                    "documento":"documento",
                    "institucionesid_institucion":"1"
                } 
            */

            if (
                !empty($data->id_apoyos) &&
                !empty($data->id_institucion) &&
                !empty($data->programa) &&
                !empty($data->descripcion) &&
                !empty($data->periodo) &&
                !empty($data->documento) &&
                !empty($data->institucionesid_institucion)
            ) {

                $becas->id_apoyos                   = $data->id_apoyos;
                $becas->id_institucion              = $data->id_institucion;
                $becas->programa                    = $data->programa;
                $becas->descripcion                 = $data->descripcion;
                $becas->periodo                     = $data->periodo;
                $becas->documento                   = $data->documento;
                $becas->institucionesid_institucion = $data->institucionesid_institucion;

                if ($becas->updateBeca()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El registro se actualizo correctamente."));
                } else {
                    http_response_code(401);
                    echo json_encode(array("status" => "Error al actualizar el registro."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede actualizar el registro."));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            /* Ver un registro en especifico:

                Se agrega { ?id=x } al final de la url
                Ejemplo: http://localhost/REST_API/controllers/becas?id=4

                { 
                    "jwt":" token ",
                }

             */
            if (isset($_GET['id'])) {

                $becas->id_apoyos = isset($_GET['id']) ? $_GET['id'] : die();
                $becas->showOne();

                if ($becas->nombre_institucion != null) {

                    $becas_arr = array(
                        "id_apoyos"                     => $becas->id_apoyos,
                        "id_institucion"                => $becas->id_institucion,
                        "programa"                      => $becas->programa,
                        "descripcion"                   => $becas->descripcion,
                        "periodo"                       => $becas->periodo,
                        "documento"                     => $becas->documento,
                        "institucionesid_institucion"   => $becas->institucionesid_institucion,
                        "nombre_institucion"            => $becas->nombre_institucion
                    );

                    http_response_code(200);
                    echo json_encode($becas_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No existe la beca."));
                }
            } else {

                // Si no se realiza la petición por ID muestra todos los registros
                // Ejemplo: http://localhost/REST_API/controllers/becas

                $stmt = $becas->becaShow();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $becas = array();
                    $becas["becas"] = array();
                    $becas["Becas registradas"] = $registro;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_apoyos"                     => $id_apoyos,
                            "id_institucion"                => $id_institucion,
                            "programa"                      => $programa,
                            "descripcion"                   => $descripcion,
                            "periodo"                       => $periodo,
                            "documento"                     => $documento,
                            "institucionesid_institucion"   => $institucionesid_institucion
                        );
                        array_push($becas["becas"], $e);
                    }
                    echo json_encode($becas);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_apoyos)) {

                $becas->id_apoyos = $data->id_apoyos;

                if ($becas->becaDelete()) {
                    http_response_code(201);
                    echo json_encode(array("status" => "La beca se elimino correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "No se puede eliminar la beca"));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Debe ingresar un ID para eliminar el registro"));
            }
        }

        // TERMINAN PETICIONES REQUEST_METHOD

    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(array("status" => "El token no es válido.", "data" => $e->getMessage()));
    }
} else {
    http_response_code(403);
    echo json_encode(array("status" => "Debe tener una sesión activa."));
}   // TERMINA VERIFICACION DE TOKEN

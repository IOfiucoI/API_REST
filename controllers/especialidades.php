<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/especialidadesModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$especialidad = new especialidad($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";


if ($jwt) {

    try {
        // $decoded verifica si el token es válido
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // INCIAN PETICIONES REQUEST_METHOD: POST, GET, PUT, DELETE

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if ( //!empty($data->id_especialidad) && 
                !empty($data->nombre) &&
                !empty($data->mision) &&
                !empty($data->vision) &&
                !empty($data->objetivo) &&
                !empty($data->id_oferta) &&
                !empty($data->carrerasid_carrera)
            ) {

                //$especialidad->id_especialidad = $data->id_especialidad;
                $especialidad->nombre               = $data->nombre;
                $especialidad->mision               = $data->mision;
                $especialidad->vision               = $data->vision;
                $especialidad->objetivo             = $data->objetivo;
                $especialidad->id_oferta            = $data->id_oferta;
                $especialidad->carrerasid_carrera   = $data->carrerasid_carrera;

                if ($especialidad->createEspecialidad()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La especialidad se registro correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al registrar la especialidad."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede crear la especialidad"));
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "PUT") {

            if (
                !empty($data->id_especialidad) &&
                !empty($data->nombre) &&
                !empty($data->mision) &&
                !empty($data->vision) &&
                !empty($data->objetivo) &&
                !empty($data->id_oferta) &&
                !empty($data->carrerasid_carrera)
            ) {

                $especialidad->id_especialidad      = $data->id_especialidad;
                $especialidad->nombre               = $data->nombre;
                $especialidad->mision               = $data->mision;
                $especialidad->vision               = $data->vision;
                $especialidad->objetivo             = $data->objetivo;
                $especialidad->id_oferta            = $data->id_oferta;
                $especialidad->carrerasid_carrera   = $data->carrerasid_carrera;

                if ($especialidad->especialidadUpdate()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La especialidad se actualizó correctamente.",));
                } else {
                    http_response_code(401);
                    echo json_encode(array("status" => "Error al actualizar la especialidad."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Debe ingresar un ID válido para actualizar la especialidad"));
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            if (isset($_GET['id'])) {

                $especialidad->id_especialidad = isset($_GET['id']) ? $_GET['id'] : die();
                $especialidad->showOne();

                if ($especialidad->nombre_carrera != null) {

                    $espec_list = array(
                        "id_especialidad"       => $especialidad->id_especialidad,
                        "nombre"                => $especialidad->nombre,
                        "mision"                => $especialidad->mision,
                        "vision"                => $especialidad->vision,
                        "objetivo"              => $especialidad->objetivo,
                        "id_oferta_"            => $especialidad->id_oferta,
                        "carrerasid_carrera"    => $especialidad->carrerasid_carrera,
                        "nombre_carrera"        => $especialidad->nombre_carrera
                    );

                    http_response_code(200);
                    echo json_encode($espec_list);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No existe la especialidad."));
                }
            } else {

                $stmt = $especialidad->especialidadShow();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $especialidad = array();
                    $especialidad["Especialidades"] = array();
                    $especialidad["Especialidades registradas"] = $registro;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_especialidad"       => $id_especialidad,
                            "nombre"                => $nombre,
                            "mision"                => $mision,
                            "vision"                => $vision,
                            "objetivo"              => $objetivo,
                            "id_oferta_"            => $id_oferta,
                            "carrerasid_carrera"    => $carrerasid_carrera,
                        );

                        array_push($especialidad["Especialidades"], $e);
                    }
                    echo json_encode($especialidad);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_especialidad)) {

                $especialidad->id_especialidad = $data->id_especialidad;

                if ($especialidad->especialidadDelete()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "Especialidad eliminada."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al eliminar la especialidad"));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Debe ingresar un ID válido para eliminar la especialidad"));
            }
        }

        // TERMINAN PETICIONES REQUEST_METHOD

    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(array("status" => "El token no es válido.", "data" => $e->getMessage()));
    }
} else {
    http_response_code(403);
    echo json_encode(array("status" => "Error: Debe tener una sesión activa."));
}

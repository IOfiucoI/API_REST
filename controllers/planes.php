<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/planesModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$planes = new planes($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";


if ($jwt) {

    try {

        $decoded = JWT::decode($jwt, $key, array('HS256'));

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (
                !empty($data->asignatura) &&
                !empty($data->clave) &&
                !empty($data->tipo_asignatura) &&
                !empty($data->id_carrera)
            ) {

                $planes->asignatura         = $data->asignatura;
                $planes->clave              = $data->clave;
                $planes->tipo_asignatura    = $data->tipo_asignatura;
                $planes->file               = $data->file;
                $planes->id_carrera         = $data->id_carrera;

                if ($planes->planCreate()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El plan de estudios se registro correctamente."));
                } else {
                    http_response_code(401);
                    echo json_encode(array("status" => "No se puede ingresar el registro."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede crear el plan"));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "PUT") {

            if (
                !empty($data->id_plan) &&
                !empty($data->asignatura) &&
                !empty($data->clave) &&
                !empty($data->tipo_asignatura) &&
                !empty($data->id_carrera)
            ) {

                $planes->id_plan            = $data->id_plan;
                $planes->asignatura         = $data->asignatura;
                $planes->clave              = $data->clave;
                $planes->tipo_asignatura    = $data->tipo_asignatura;
                $planes->id_carrera         = $data->id_carrera;

                $planes->ruta_pdf           = $data->ruta_pdf;
                $planes->file               = $data->file;

                if (empty($planes->file)) {
                    $planes->planUpdateData();
                    http_response_code(200);
                    echo json_encode(array("status" => "El plan de estudios se actualizo correctamente."));
                    return false;
                }

                if (!empty($planes->file)) {

                    $planes->planUpdateFile();
                    http_response_code(200);
                    echo json_encode(array("status" => "El plan de estudios se actualizo correctamente."));
                } else {
                    http_response_code(401);
                    echo json_encode(array("status" => "No se puede actualizar el registro."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede actualizar el plan"));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            if (isset($_GET['id'])) {

                $planes->id_plan = isset($_GET['id']) ? $_GET['id'] : die();
                $planes->showOne();

                if ($planes->nombre_carrera != null) {
                    // create array
                    $plan_arr = array(
                        "id_plan"         => $planes->id_plan,
                        "asignatura"      => $planes->asignatura,
                        "clave"           => $planes->clave,
                        "tipo_asignatura" => $planes->tipo_asignatura,
                        "ruta_pdf"        => $planes->ruta_pdf,
                        "id_carrera"      => $planes->id_carrera,
                        "nombre_carrera"  => $planes->nombre_carrera
                    );

                    http_response_code(200);
                    echo json_encode($plan_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No existe la asignatura."));
                }
            } else {

                $datos = $planes->planShow();
                $i = $datos->rowCount();

                if ($i > 0) {
                    $planes = array();
                    $planes["Planes"] = array();
                    $planes["Planes registrados"] = $i;

                    while ($row = $datos->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_plan"           => $id_plan,
                            "asignatura"        => $asignatura,
                            "clave"             => $clave,
                            "tipo_asignatura"   => $tipo_asignatura,
                            "ruta_pdf"          => $ruta_pdf,
                            "id_carrera"        => $id_carrera
                        );
                        array_push($planes["Planes"], $e);
                    }
                    echo json_encode($planes);
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_plan)) {

                $planes->id_plan = $data->id_plan;

                if ($planes->planDelete()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El Plan de estudios se elimino correctamente."));
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "Error: No se puede eliminar el registro."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Ingresa un ID válido para eliminar el plan"));
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

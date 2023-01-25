<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/carrerasModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$carrera = new carreras($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";


if ($jwt) {

    try {
        // $decoded verifica si el token es válido
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // INCIAN PETICIONES REQUEST_METHOD: POST, GET, PUT, DELETE

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (
                !empty($data->nombre) &&
                !empty($data->resenia) &&
                !empty($data->objetivo) &&
                !empty($data->mision) &&
                !empty($data->vision) &&
                !empty($data->porque) &&
                !empty($data->perfil_ingreso) &&
                !empty($data->perfil_egreso) &&
                !empty($data->campo_laboral) &&
                !empty($data->institucionesid_institucion)
            ) {

                $carrera->nombre                        = $data->nombre;
                $carrera->resenia                       = $data->resenia;
                $carrera->objetivo                      = $data->objetivo;
                $carrera->mision                        = $data->mision;
                $carrera->vision                        = $data->vision;
                $carrera->porque                        = $data->porque;
                $carrera->perfil_ingreso                = $data->perfil_ingreso;
                $carrera->perfil_egreso                 = $data->perfil_egreso;
                $carrera->campo_laboral                 = $data->campo_laboral;
                $carrera->institucionesid_institucion   = $data->institucionesid_institucion;

                if ($carrera->createCarrera()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La carrera se registro correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al registrar la carrera."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede crear la carrera."));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "PUT") {

            if (
                !empty($data->id_carrera) &&
                !empty($data->nombre) &&
                !empty($data->resenia) &&
                !empty($data->objetivo) &&
                !empty($data->mision) &&
                !empty($data->vision) &&
                !empty($data->porque) &&
                !empty($data->perfil_ingreso) &&
                !empty($data->perfil_egreso) &&
                !empty($data->campo_laboral) &&
                !empty($data->institucionesid_institucion)
            ) {

                $carrera->id_carrera                    = $data->id_carrera;
                $carrera->nombre                        = $data->nombre;
                $carrera->resenia                       = $data->resenia;
                $carrera->objetivo                      = $data->objetivo;
                $carrera->mision                        = $data->mision;
                $carrera->vision                        = $data->vision;
                $carrera->porque                        = $data->porque;
                $carrera->perfil_ingreso                = $data->perfil_ingreso;
                $carrera->perfil_egreso                 = $data->perfil_egreso;
                $carrera->campo_laboral                 = $data->campo_laboral;
                $carrera->institucionesid_institucion   = $data->institucionesid_institucion;

                if ($carrera->carreraUpdate()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El carrera se actualizo correctamente.",));
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "Erro: No se puede actualizar el la carrera."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Datos incompletos, no se puede actualizar la carrera."));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            if (isset($_GET['id'])) {

                $carrera->id_carrera = isset($_GET['id']) ? $_GET['id'] : die();
                $carrera->showOne();

                if ($carrera->nombre_institucion != null) {
                    // create array
                    $carrera_arr = array(
                        "id_carrera"                    => $carrera->id_carrera,
                        "nombre"                        => $carrera->nombre,
                        "resenia"                       => $carrera->resenia,
                        "objetivo"                      => $carrera->objetivo,
                        "mision"                        => $carrera->mision,
                        "vision"                        => $carrera->vision,
                        "porque"                        => $carrera->porque,
                        "perfil_ingreso"                => $carrera->perfil_ingreso,
                        "perfil_egreso"                 => $carrera->perfil_egreso,
                        "campo_laboral"                 => $carrera->campo_laboral,
                        "institucionesid_institucion"   => $carrera->institucionesid_institucion,
                        "nombre_institucion"            => $carrera->nombre_institucion
                    );

                    http_response_code(200);
                    echo json_encode($carrera_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No existe la carrera."));
                }
            } else {

                $stmt = $carrera->carreraShow();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $carrera = array();
                    $carrera["Carreras"] = array();
                    $carrera["Carreras Registradas:"] = $registro;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_carrea"                     => $id_carrera,
                            "nombre"                        => $nombre,
                            "resenia"                       => $resenia,
                            "objetivo"                      => $objetivo,
                            "mision"                        => $mision,
                            "vision"                        => $vision,
                            "porque"                        => $porque,
                            "perfil_ingreso"                => $perfil_ingreso,
                            "perfil_egreso"                 => $perfil_egreso,
                            "campo_laboral"                 => $campo_laboral,
                            "institucionesid_institucion"   => $institucionesid_institucion,
                        );
                        array_push($carrera["Carreras"], $e);
                    }
                    http_response_code(200);
                    echo json_encode($carrera);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_carrera)) {

                $carrera->id_carrera = $data->id_carrera;

                if ($carrera->carreraDelete()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La carrera se elimino correctamente"));
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "Error: No se puede eliminar la carrera."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Debe ingresar un ID válido para eliminar la carrera"));
            }
        } // TERMINAN PETICIONES REQUEST_METHOD


    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(array("status" => "El token no es válido.", "data" => $e->getMessage()));
    }
} else {
    http_response_code(403);
    echo json_encode(array("status" => "Error: Debe tener una sesión activa."));
}

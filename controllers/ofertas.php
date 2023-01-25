<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/ofertaModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$oferta = new oferta($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {

    try {
        // $decoded verifica si el token es válido
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // INCIAN PETICIONES REQUEST_METHOD: POST, GET, PUT, DELETE
        if ($_SERVER["REQUEST_METHOD"] == "POST") {


            if (
                !empty($data->id_carrera) &&
                !empty($data->id_ext) &&
                !empty($data->cupo_x_carrera)
            ) {

                $oferta->id_carrera = $data->id_carrera;
                $oferta->id_ext = $data->id_ext;
                $oferta->cupo_x_carrera = $data->cupo_x_carrera;

                if ($oferta->ofertaCreate()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La oferta se registro correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al registrar la oferta."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => " Datos incompletos, no se puede crear la oferta "));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "PUT") {

            if (
                !empty($data->id_ofe_ext) &&
                !empty($data->id_carrera) &&
                !empty($data->id_ext) &&
                !empty($data->cupo_x_carrera)
            ) {

                $oferta->id_ofe_ext = $data->id_ofe_ext;
                $oferta->id_carrera = $data->id_carrera;
                $oferta->id_ext = $data->id_ext;
                $oferta->cupo_x_carrera = $data->cupo_x_carrera;

                if ($oferta->ofertaUpdate()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El registro se actualizo correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "No se puede actualizar el registro."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => " Datos incompletos, no se puede actualizar la oferta "));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            if (isset($_GET['id'])) {

                $oferta->id_ofe_ext = isset($_GET['id']) ? $_GET['id'] : die();
                $oferta->showOne();

                if ($oferta->nombre_carrera != null) {
                    // create array
                    $media_arr = array(
                        "id_ofe_ext"         => $oferta->id_ofe_ext,
                        "id_carrera"         => $oferta->id_carrera,
                        "nombre_carrera"     => $oferta->nombre_carrera,
                        "id_ext"             => $oferta->id_ext,
                        "nombre_extension"   => $oferta->nombre_extension,
                        "cupo_x_carrera "    => $oferta->cupo_x_carrera
                    );

                    http_response_code(200);
                    echo json_encode($media_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No existen ofertas."));
                }
            } else {

                $stmt = $oferta->ofertaShow();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $ofertas = array();
                    $ofertas["Ofertas"] = array();
                    $ofertas["Ofertas registradas"] = $registro;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_ofe_ext"        => $id_ofe_ext,
                            "id_arrera"         => $id_carrera,
                            "id_ext"            => $id_ext,
                            "cupo_x_carrera"    => $cupo_x_carrera
                        );

                        array_push($ofertas["Ofertas"], $e);
                    }
                    echo json_encode($ofertas);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_ofe_ext)) {

                $oferta->id_ofe_ext = $data->id_ofe_ext;

                if ($oferta->ofertaDelete()) {
                    echo json_encode(array("status" => "Oferta eliminada."));
                } else {
                    echo json_encode(array("status" => "Error al eliminar la oferta"));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => " Debe ingresar el ID de la oferta que desea eliminar"));
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

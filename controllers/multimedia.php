<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/multimediaModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$media = new media($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {

    try { // Validación de token
        $decoded = JWT::decode($jwt, $key, array('HS256'));


        // INCIAN PETICIONES 

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (
                !empty($data->nombre_recurso) &&
                !empty($data->imagen) &&
                !empty($data->ext_recurso) &&
                !empty($data->institucionesid_institucion)
            ) {

                $media->nombre_recurso = $data->nombre_recurso;
                $media->ext_recurso    = $data->ext_recurso;
                $media->institucionesid_institucion = $data->institucionesid_institucion;
                $media->imagen         = $data->imagen;

                $media->mediaCreate();
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Verifique que los datos esten completos, no se puede crear el recurso."));
                return false;
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "PUT") {

            if (
                !empty($data->id_multimedia) &&
                !empty($data->nombre_recurso) &&
                !empty($data->ext_recurso) &&
                !empty($data->institucionesid_institucion)
            ) {

                $media->id_multimedia               = $data->id_multimedia;
                $media->nombre_recurso              = $data->nombre_recurso;
                $media->ruta                        = $data->ruta;
                $media->imagen                      = $data->imagen;
                $media->ext_recurso                 = $data->ext_recurso;
                $media->institucionesid_institucion = $data->institucionesid_institucion;

                if (empty($media->imagen)) {

                    $media->mediaUpdateData();
                    http_response_code(200);
                    echo json_encode(array("status" => "El registro se actualizo correctamente."));
                    return false;
                }

                if (!empty($media->imagen)) {

                    $media->mediaUpdateFile();
                    http_response_code(200);
                    echo json_encode(array("status" => "El registro se actualizo correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "No se puede actualizar el registro."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Verifique que los datos esten completos, no se puede actualizar el recurso multimedia."));
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            if (isset($_GET['id'])) {

                $media->id_multimedia = isset($_GET['id']) ? $_GET['id'] : die();
                $media->showOne();

                if ($media->nombre_institucion != null) {

                    $media_arr = array(
                        "id_multimedia"                 => $media->id_multimedia,
                        "nombre_recurso"                => $media->nombre_recurso,
                        "ruta"                          => $media->ruta,
                        "ext_recurso"                   => $media->ext_recurso,
                        "institucionesid_institucion"   => $media->institucionesid_institucion,
                        "nombre_institucion"            => $media->nombre_institucion
                    );

                    http_response_code(200);
                    echo json_encode($media_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "El recurso no existe."));
                    return false;
                }
            } else {

                $stmt = $media->mediaShow();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $multimedia = array();
                    $multimedia["multimedia"] = array();
                    $multimedia["Recursos registrados"]     = $registro;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $e = array(
                            "id_multimedia"                 => $id_multimedia,
                            "nombre_recurso"                => $nombre_recurso,
                            "ruta"                          => $ruta,
                            "ext_recurso"                   => $ext_recurso,
                            "institucionesid_institucion"   => $institucionesid_institucion
                        );
                        array_push($multimedia["multimedia"], $e);
                    }
                    echo json_encode($multimedia);
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "No se encontraron registros."));
                    return false;
                }
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_multimedia)) {

                $media->id_multimedia = $data->id_multimedia;

                if ($media->mediaDelete()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "Recurso multimedia eliminado correctamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al eliminar el recurso multimedia"));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Ingrese el ID del recurso multimedia que desea eliminar"));
            }
        }

        // TERMINAN PETICIONES

    } catch (Exception $e) {
        // Mensaje de error si el token no es válido
        http_response_code(401);
        echo json_encode(array(
            "status" => "Token inválido.", "data" => $e->getMessage()
        ));
    }
} else {
    // Si no se inicia sesión con token enviara mensaje
    http_response_code(401);
    echo json_encode(array("status" => "Error: Debe tener una sesión activa."));
}

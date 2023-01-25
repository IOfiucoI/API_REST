<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/extensionesModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$extensiones = new extensiones($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        !empty($data->id_institucion) &&
        !empty($data->nombre_extesion) &&
        !empty($data->direccion) &&
        !empty($data->latitud) &&
        !empty($data->longitud)
    ) {

        $extensiones->id_institucion    = $data->id_institucion;
        $extensiones->nombre_extesion   = $data->nombre_extesion;
        $extensiones->direccion         = $data->direccion;
        $extensiones->latitud           = $data->latitud;
        $extensiones->longitud          = $data->longitud;

        if ($extensiones->extensionCreate()) {
            http_response_code(200);
            echo json_encode(array("status" => "La extension se registro correctamente."));
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "Error al registrar la extension."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "Datos incompletos, no se puede crear la extensión "));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    if (
        !empty($data->id_ext) &&
        !empty($data->id_institucion) &&
        !empty($data->nombre_extesion) &&
        !empty($data->direccion) &&
        !empty($data->latitud) &&
        !empty($data->longitud)
    ) {

        $extensiones->id_ext            = $data->id_ext;
        $extensiones->id_institucion    = $data->id_institucion;
        $extensiones->nombre_extesion   = $data->nombre_extesion;
        $extensiones->direccion         = $data->direccion;
        $extensiones->latitud           = $data->latitud;
        $extensiones->longitud          = $data->longitud;

        if ($extensiones->extensionUpdate()) {
            http_response_code(200);
            echo json_encode(array("status" => "La extensión se actualizo correctamente."));
        } else {
            http_response_code(401);
            echo json_encode(array("status" => "No se pudo actualizar el la extensión."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "Datos incompletos, no se puede actualizar la extensión"));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['id'])) {

        $extensiones->id_ext = isset($_GET['id']) ? $_GET['id'] : die();
        $extensiones->showOne();

        if ($extensiones->nombre_institucion != null) {

            $ext_arr = array(

                "id_ext"                => $extensiones->id_ext,
                "id_institucion"        => $extensiones->id_institucion,
                "nombre_institucion"    => $extensiones->nombre_institucion,
                "nombre_extesion"       => $extensiones->nombre_extesion,
                "direccion"             => $extensiones->direccion,
                "latitud"               => $extensiones->latitud,
                "longitud"              => $extensiones->longitud
            );

            http_response_code(200);
            echo json_encode($ext_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("status" => "No existe la extensión."));
        }
    } else {

        $stmt = $extensiones->extensionShow();
        $registro = $stmt->rowCount();

        if ($registro > 0) {
            $ext = array();
            $ext["Extensiones"] = array();
            $ext["Extensiones Registradas:"] = $registro;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $e = array(
                    "id_ext"            => $id_ext,
                    "id_institucion"    => $id_institucion,
                    "nombre_extesion"   => $nombre_extesion,
                    "direccion"         => $direccion,
                    "latitud"           => $latitud,
                    "longitud"          => $longitud
                );
                array_push($ext["Extensiones"], $e);
            }
            echo json_encode($ext);
        } else {
            http_response_code(404);
            echo json_encode(array("status" => "No se encontraron registros."));
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    if (!empty($data->id_ext)) {

        $extensiones->id_ext = $data->id_ext;

        if ($extensiones->extensionDelete()) {
            echo json_encode(array("status" => "Registro eliminado."));
        } else {
            echo json_encode(array("status" => "Error al eliminar el registro"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "Ingrese un ID para eliminar la extensión"));
    }
}

        // TERMINAN PETICIONES REQUEST_METHOD
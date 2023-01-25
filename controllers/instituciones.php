<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../models/db.php';
include_once '../models/institucionesModel.php';

include_once '_JWT.php';
include_once '_JWT_BeforeValidException.php';
include_once '_JWT_Core.php';
include_once '_JWT_ExpiredException.php';
include_once '_JWT_SignatureInvalidException.php';

use \Firebase\JWT\JWT;

$database = new DB();
$db = $database->connect();
$instituciones = new instituciones($db);
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";


if ($jwt) {

    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));


        /*
        CREAR INSTITUCION:

        "jwt":"",
        "nombre":"ITSZ",
        "pagina_web":"http://",
        "link_video_inst":"http://youtube.com/",
        "direccion":"Avenida",
        "localidad":"Nogales",
        "municipio":"Nogales",
        "modalidad":"Mixto",
        "turno":"Matutino",
        "telefono":"1234567890",
        "correo_contacto":"166w0454@zongolica.tecnm.mx",
        "cupo_total":"100"
        */

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (
                !empty($data->nombre) &&
                !empty($data->pagina_web) &&
                !empty($data->link_video_inst) &&
                !empty($data->direccion) &&
                !empty($data->localidad) &&
                !empty($data->municipio) &&
                !empty($data->modalidad) &&
                !empty($data->turno) &&
                !empty($data->telefono) &&
                !empty($data->correo_contacto) &&
                !empty($data->cupo_total)
            ) {
                $instituciones->nombre          = $data->nombre;
                $instituciones->pagina_web      = $data->pagina_web;
                $instituciones->link_video_inst = $data->link_video_inst;
                $instituciones->direccion       = $data->direccion;
                $instituciones->localidad       = $data->localidad;
                $instituciones->municipio       = $data->municipio;
                $instituciones->modalidad       = $data->modalidad;
                $instituciones->turno           = $data->turno;
                $instituciones->telefono        = $data->telefono;
                $instituciones->correo_contacto = $data->correo_contacto;
                $instituciones->cupo_total      = $data->cupo_total;

                if ($instituciones->createInstitucion()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La institución se agrego con éxito"));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al agregar la institucion"));
                }
            } else {
                echo json_encode(array("status" => "Verique que los datos esten completos"));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            if (isset($_GET['id'])) {

                $instituciones->id = isset($_GET['id']) ? $_GET['id'] : die();
                $instituciones->readOne();

                if ($instituciones->nombre != null) {

                    $registro = array(
                        "id_institucion"   =>   $instituciones->id_institucion,
                        "nombre"           =>   $instituciones->nombre,
                        "pagina_web"       =>   $instituciones->pagina_web,
                        "link_video_inst"  =>   $instituciones->link_video_inst,
                        "direccion"        =>   $instituciones->direccion,
                        "localidad"        =>   $instituciones->localidad,
                        "municipio"        =>   $instituciones->municipio,
                        "modalidad"        =>   $instituciones->modalidad,
                        "turno"            =>   $instituciones->turno,
                        "telefono"         =>   $instituciones->telefono,
                        "correo_contacto"  =>   $instituciones->correo_contacto,
                        "cupo_total"       =>   $instituciones->cupo_total
                    );

                    http_response_code(200);
                    echo json_encode($registro);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "La institucion no existe."));
                }
            } else {

                $stmt = $instituciones->readAll();
                $registro = $stmt->rowCount();

                if ($registro > 0) {
                    $escuelas = array();
                    $escuelas["instituciones registradas"] = $registro;
                    $escuelas["instituciones"] = array();


                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $i = array(
                            "id_institucion"   =>   $id_institucion,
                            "nombre"           =>   $nombre,
                            "pagina_web"       =>   $pagina_web,
                            "link_video_inst"  =>   $link_video_inst,
                            "direccion"        =>   $direccion,
                            "localidad"        =>   $localidad,
                            "municipio"        =>   $municipio,
                            "modalidad"        =>   $modalidad,
                            "turno"            =>   $turno,
                            "telefono"         =>   $telefono,
                            "correo_contacto"  =>   $correo_contacto,
                            "cupo_total"       =>   $cupo_total

                        );
                        array_push($escuelas["instituciones"], $i);
                    }
                    echo json_encode($escuelas);
                } else {
                    http_response_code(404);
                    echo json_encode(array("status" => "No se encontraron registros."));
                }
            }
        }

        /*
        EJEMPLO Actualización de registro:
        {
                    "jwt":" token5454654654 ",
                    "id_institucion": "3",
                    "nombre": "update",
                    "pagina_web": "http://",
                    "link_video_inst": "http://youtube.com/",
                    "direccion": "Avenida",
                    "localidad": "Nogales",
                    "municipio": "Nogales",
                    "modalidad": "Mixto",
                    "turno": "Matutino",
                    "telefono": "1234567890",
                    "correo_contacto": "mail@mail.com",
                    "cupo_total": "1"
        }
        */

        if ($_SERVER["REQUEST_METHOD"] == "PUT") {
            if (
                !empty($data->id_institucion) &&
                !empty($data->nombre) &&
                !empty($data->pagina_web) &&
                !empty($data->link_video_inst) &&
                !empty($data->direccion) &&
                !empty($data->localidad) &&
                !empty($data->municipio) &&
                !empty($data->modalidad) &&
                !empty($data->turno) &&
                !empty($data->telefono) &&
                !empty($data->correo_contacto) &&
                !empty($data->cupo_total)
            ) {
                $instituciones->id_institucion  = $data->id_institucion;
                $instituciones->nombre          = $data->nombre;
                $instituciones->pagina_web      = $data->pagina_web;
                $instituciones->link_video_inst = $data->link_video_inst;
                $instituciones->direccion       = $data->direccion;
                $instituciones->localidad       = $data->localidad;
                $instituciones->municipio       = $data->municipio;
                $instituciones->modalidad       = $data->modalidad;
                $instituciones->turno           = $data->turno;
                $instituciones->telefono        = $data->telefono;
                $instituciones->correo_contacto = $data->correo_contacto;
                $instituciones->cupo_total      = $data->cupo_total;

                if ($instituciones->updateInstitucion()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "La institución se actualizo con éxito"));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al actualizar la institucion"));
                }
            } else {
                echo json_encode(array("status" => "Verique que los datos esten completos"));
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

            if (!empty($data->id_institucion)) {

                $instituciones->id_institucion = $data->id_institucion;

                if ($instituciones->deleteInstitucion()) {
                    http_response_code(200);
                    echo json_encode(array("status" => "El instituto se eliminó correctamente"));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "Error al eliminar el instituto"));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "Ingresa el id que deseas eliminar"));
            }
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array(
            "status" => "Token inválido.", "error" => $e->getMessage()
        ));
    }
} else {
    // Si no se inicia sesión con token enviara mensaje
    http_response_code(401);
    echo json_encode(array("status" => "Debe tener una sesión activa."));
}

<?php

include_once '../models/db.php';
include_once '../models/multimediaModel.php';

$database = new DB();
$db = $database->connect();
$media = new media($db);
$data = json_decode(file_get_contents("php://input"));

$name       = $_FILES["image"]["name"];
$type       = $_FILES["image"]["type"];
$size       = $_FILES["image"]["size"];
$tmp_name   = $_FILES["image"]["tmp_name"];


if ($type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png') {


    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $filename   = date("Ymd_His") . "." . $ext;
    $location   = "../images/" . $filename;
    $mb         = 1;
    $bytes      = 1048576;
    $maxSize    = $bytes * $mb;

    $dataIMG = array(
        'nombre_recurso'              => $_POST['nombre_recurso'],
        'ruta'                        => $location,
        'ext_recurso'                 => $_POST['ext_recurso'],
        'institucionesid_institucion' => $_POST['institucionesid_institucion']
    );

    $dataIMGConversion =  json_decode(json_encode($dataIMG));

    $media->nombre_recurso              = $dataIMGConversion->nombre_recurso;
    $media->ruta                        = $dataIMGConversion->ruta;
    $media->ext_recurso                 = $dataIMGConversion->ext_recurso;
    $media->institucionesid_institucion = $dataIMGConversion->institucionesid_institucion;

    if ($size < $maxSize) {

        if ($media->mediaCreate() && move_uploaded_file($tmp_name, $location)) {
            echo json_encode(array(
                "Status" => "Se agrego recurso multimedia",
                "url" => $location
            ));
        } else {
            echo json_encode(array("Status" => "No se puede crear el recurso multimedia."));
        }
    } else {
        echo json_encode(array("Status" => "Límite excedido", "Peso máximo en $mg MB."));
    }
} else {
    echo json_encode(array("Status" => "Sólo se aceptan formatos JPG y PNG."));
}

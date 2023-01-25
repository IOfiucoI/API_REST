<?php

class media
{

    private $conn;
    private $table_name = "multimedia";

    public $id_multimedia;
    public $nombre_recurso;
    public $ruta;
    public $ext_recurso;
    public $institucionesid_institucion;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    function mediaCreate()
    {

        $query = "INSERT INTO " . $this->table_name . "
                        SET nombre_recurso = :nombre_recurso,
                        ruta = :ruta, ext_recurso = :ext_recurso,
                        institucionesid_institucion = :institucionesid_institucion ";

        $stmt = $this->conn->prepare($query);

        $this->nombre_recurso = htmlspecialchars(strip_tags($this->nombre_recurso));
        $this->ext_recurso = htmlspecialchars(strip_tags($this->ext_recurso));
        $this->institucionesid_institucion = htmlspecialchars(strip_tags($this->institucionesid_institucion));

        $imageFile = base64_decode($this->imagen);
        $type = finfo_buffer(finfo_open(), $imageFile, FILEINFO_MIME_TYPE);
        $extension = substr($type, 6);
        $size = (int) (strlen(rtrim($imageFile, '=')) * 3 / 4);
        $bytes      = 1048576;
        $mb         = 1;
        $maxSize    = $mb * $bytes;
        $fileUpload = "../files/images/" . uniqid() . '.' . $extension;

        if ($extension == "jpeg" || $extension == "jpg") {

            if ($size < $maxSize) {

                file_put_contents($fileUpload, $imageFile);
                echo json_encode(array(
                    "status" => "El registro se realizo corectamente",
                    "url" => $fileUpload
                ));
            } else {
                echo json_encode(array("Status" => "Limite excedido.", "Peso Máximo en MB" => $mb));
                return false;
            }
        } else {
            echo json_encode(array("Status" => "Sólo se admiten formatos JPG y JPEG"));
            return false;
        }

        $stmt->bindParam(':nombre_recurso', $this->nombre_recurso);
        $stmt->bindParam(':ruta', $fileUpload);
        $stmt->bindParam(':ext_recurso', $this->ext_recurso);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function mediaUpdateData()
    {
        $query = " UPDATE " . $this->table_name . " SET nombre_recurso = :nombre_recurso,
                    ruta = :ruta,
                    ext_recurso = :ext_recurso,
                    institucionesid_institucion = :institucionesid_institucion
                    WHERE id_multimedia = :id_multimedia";

        $stmt = $this->conn->prepare($query);


        $this->id_multimedia = htmlspecialchars(strip_tags($this->id_multimedia));
        $this->nombre_recurso = htmlspecialchars(strip_tags($this->nombre_recurso));
        $this->ruta = htmlspecialchars(strip_tags($this->ruta));
        $this->ext_recurso = htmlspecialchars(strip_tags($this->ext_recurso));
        $this->institucionesid_institucion = htmlspecialchars(strip_tags($this->institucionesid_institucion));

        $stmt->bindParam(':id_multimedia',               $this->id_multimedia);
        $stmt->bindParam(':nombre_recurso',              $this->nombre_recurso);
        $stmt->bindParam(':ruta',                        $this->ruta);
        $stmt->bindParam(':ext_recurso',                 $this->ext_recurso);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function mediaUpdateFile()
    {

        $querySelect = " SELECT * FROM " . $this->table_name . " WHERE id_multimedia = :id_multimedia";
        $stmtSelect = $this->conn->prepare($querySelect);
        $stmtSelect->execute(array(":id_multimedia" => $this->id_multimedia));
        $rowLocation = $stmtSelect->fetch(PDO::FETCH_ASSOC);


        $query = " UPDATE " . $this->table_name . " SET nombre_recurso = :nombre_recurso,
                    ruta = :ruta,
                    ext_recurso = :ext_recurso,
                    institucionesid_institucion = :institucionesid_institucion
                    WHERE id_multimedia = :id_multimedia";

        $stmt = $this->conn->prepare($query);


        $this->id_multimedia = htmlspecialchars(strip_tags($this->id_multimedia));
        $this->nombre_recurso = htmlspecialchars(strip_tags($this->nombre_recurso));
        $this->ext_recurso = htmlspecialchars(strip_tags($this->ext_recurso));
        $this->institucionesid_institucion = htmlspecialchars(strip_tags($this->institucionesid_institucion));


        $imageFile = base64_decode($this->imagen);
        $type = finfo_buffer(finfo_open(), $imageFile, FILEINFO_MIME_TYPE);
        $extension = substr($type, 6);
        $size = (int) (strlen(rtrim($imageFile, '=')) * 3 / 4);
        $bytes      = 1048576;
        $mb         = 1;
        $maxSize    = $mb * $bytes;
        $fileUpload = "../files/images/" . uniqid() . '.' . $extension;

        if ($extension == "jpeg" || $extension == "jpg") {

            if ($size < $maxSize) {
                unlink($rowLocation['ruta']);
                file_put_contents($fileUpload, $imageFile);
                echo json_encode(array(
                    "status" => "El registro se realizo corectamente",
                    "url" => $fileUpload
                ));
            } else {
                echo json_encode(array("Status" => "Limite excedido.", "Peso Máximo en MB" => $mb));
                return false;
            }
        } else {
            echo json_encode(array("Status" => "Sólo se admiten formatos JPG y JPEG"));
            return false;
        }

        $stmt->bindParam(':id_multimedia',      $this->id_multimedia);
        $stmt->bindParam(':nombre_recurso',     $this->nombre_recurso);
        $stmt->bindParam(':ruta',               $fileUpload);
        $stmt->bindParam(':ext_recurso',        $this->ext_recurso);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function mediaDelete()
    {

        $querySelect = " SELECT * FROM " . $this->table_name . " WHERE id_multimedia = :id_multimedia";
        $stmtSelect = $this->conn->prepare($querySelect);
        $stmtSelect->execute(array(":id_multimedia" => $this->id_multimedia));
        $rowLocation = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        $imagen = $rowLocation['ruta'];

        if (file_exists($imagen)) {
            unlink($imagen);
        } else {
            echo json_encode(array("status" => "No se encontro la imagen en la ruta $imagen"));
        }

        $query = "DELETE  FROM " . $this->table_name . " WHERE id_multimedia = ?";
        $stmt = $this->conn->prepare($query);
        $this->id_multimedia = htmlspecialchars(strip_tags($this->id_multimedia));
        $stmt->bindParam(1, $this->id_multimedia);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    function mediaShow()
    {
        $query = " SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function showOne()
    {
        $query = " SELECT m.nombre_recurso,
                              m.ruta, m.ext_recurso,
                              m.institucionesid_institucion,
                              i.nombre AS nombre_institucion 
                       FROM " . $this->table_name . " m 
                              INNER JOIN instituciones i ON m.institucionesid_institucion = i.id_institucion
                              WHERE m.id_multimedia = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_multimedia);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->nombre_recurso = $row['nombre_recurso'];
        $this->ruta = $row['ruta'];
        $this->ext_recurso = $row['ext_recurso'];
        $this->institucionesid_institucion = $row['institucionesid_institucion'];
        $this->nombre_institucion = $row['nombre_institucion'];
    }
}

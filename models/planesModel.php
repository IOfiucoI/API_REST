<?php

class planes
{

    private $conn;
    private $table_name = "planes_estudio";

    public $id_plan;
    public $asignatura;
    public $clave;
    public $tipo_asignatura;
    public $ruta_pdf;
    public $id_carrera;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function planCreate()
    {
        $query = "INSERT INTO " . $this->table_name . "
            SET asignatura = :asignatura, 
                clave = :clave, 
                tipo_asignatura = :tipo_asignatura, 
                ruta_pdf = :ruta_pdf,
                id_carrera = :id_carrera";
        $stmt = $this->conn->prepare($query);

        $this->asignatura = htmlspecialchars(strip_tags($this->asignatura));
        $this->clave = htmlspecialchars(strip_tags($this->clave));
        $this->tipo_asignatura = htmlspecialchars(strip_tags($this->tipo_asignatura));
        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));


        $pdf = base64_decode($this->file);
        $type = finfo_buffer(finfo_open(), $pdf, FILEINFO_MIME_TYPE);
        $extension = substr($type, 12);
        $size = (int) (strlen(rtrim($pdf, '=')) * 3 / 4);
        $bytes      = 1048576;
        $mb         = 1;
        $maxSize    = $bytes * $mb;
        $fileUpload = "../files/pdf/" . uniqid() . '.' . $extension;

        if ($extension == "pdf") {
            if ($size < $maxSize) {
                file_put_contents($fileUpload, $pdf);
                echo json_encode(array("url"=>$fileUpload));
            } else {
                echo json_encode(array("Status" => "Limite excedido.", "Peso Máximo por archivo $mb MB" ));
                return false;
            }
        } else {
            echo json_encode(array("Status" => "Sólo se admite formato PDF"));
            return false;
        }

        $stmt->bindParam(':asignatura', $this->asignatura);
        $stmt->bindParam(':clave', $this->clave);
        $stmt->bindParam(':tipo_asignatura', $this->tipo_asignatura);
        $stmt->bindParam(':ruta_pdf', $fileUpload);
        $stmt->bindParam(':id_carrera', $this->id_carrera);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function planUpdateData()
    {

        $query = "UPDATE " . $this->table_name . "
            SET asignatura = :asignatura, 
                clave = :clave, 
                tipo_asignatura = :tipo_asignatura, 
                ruta_pdf = :ruta_pdf,
                id_carrera = :id_carrera
                WHERE id_plan = :id_plan ";
        $stmt = $this->conn->prepare($query);

        $this->id_plan = htmlspecialchars(strip_tags($this->id_plan));
        $this->asignatura = htmlspecialchars(strip_tags($this->asignatura));
        $this->clave = htmlspecialchars(strip_tags($this->clave));
        $this->tipo_asignatura = htmlspecialchars(strip_tags($this->tipo_asignatura));
        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));

        $stmt->bindParam(':id_plan', $this->id_plan);
        $stmt->bindParam(':asignatura', $this->asignatura);
        $stmt->bindParam(':clave', $this->clave);
        $stmt->bindParam(':tipo_asignatura', $this->tipo_asignatura);
        $stmt->bindParam(':ruta_pdf', $this->ruta_pdf);
        $stmt->bindParam(':id_carrera', $this->id_carrera);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function planUpdateFile()
    {

        $querySelect = " SELECT * FROM " . $this->table_name . " WHERE id_plan = :id_plan";
        $stmtSelect = $this->conn->prepare($querySelect);
        $stmtSelect->execute(array(":id_plan" => $this->id_plan));
        $rowLocation = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        $archivo = $rowLocation['ruta_pdf'];

        $query = "UPDATE " . $this->table_name . "
            SET asignatura = :asignatura, 
                clave = :clave, 
                tipo_asignatura = :tipo_asignatura, 
                ruta_pdf = :ruta_pdf,
                id_carrera = :id_carrera
                WHERE id_plan = :id_plan ";
        $stmt = $this->conn->prepare($query);

        $this->id_plan = htmlspecialchars(strip_tags($this->id_plan));
        $this->asignatura = htmlspecialchars(strip_tags($this->asignatura));
        $this->clave = htmlspecialchars(strip_tags($this->clave));
        $this->tipo_asignatura = htmlspecialchars(strip_tags($this->tipo_asignatura));
        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));

        $pdf        = base64_decode($this->file);
        $type       = finfo_buffer(finfo_open(), $pdf, FILEINFO_MIME_TYPE);
        $extension  = substr($type, 12);
        $size       = (int) (strlen(rtrim($pdf, '=')) * 3 / 4);
        $bytes      = 1048576;
        $mb         = 1;
        $maxSize    = $bytes * $mb;
        $fileUpload = "../files/pdf/" . uniqid() . '.' . $extension;

        if ($extension == "pdf") {
            if ($size < $maxSize) {
                file_put_contents($fileUpload, $pdf);
            } else {
                echo json_encode(array("Status" => "Limite excedido.", "Peso Máximo de archivo $mb MB"));
                return false;
            }
        } else {
            echo json_encode(array("Status" => "Sólo se admiten formatos PDF"));
        }

        $stmt->bindParam(':id_plan',            $this->id_plan);
        $stmt->bindParam(':asignatura',         $this->asignatura);
        $stmt->bindParam(':clave',              $this->clave);
        $stmt->bindParam(':tipo_asignatura',    $this->tipo_asignatura);
        $stmt->bindParam(':ruta_pdf',           $fileUpload);
        $stmt->bindParam(':id_carrera',         $this->id_carrera);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function planDelete()
    {

        $querySelect = " SELECT * FROM " . $this->table_name . " WHERE id_plan = :id_plan";
        $stmtSelect = $this->conn->prepare($querySelect);
        $stmtSelect->execute(array(":id_plan" => $this->id_plan));
        $rowLocation = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        $archivo = $rowLocation['ruta_pdf'];

        if (file_exists($archivo)) {
            unlink($archivo);
        } else {
            echo json_encode(array("status" => "No se encontro el archivo en la ruta: $archivo"));
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id_plan = ?";
        $stmt = $this->conn->prepare($query);
        $this->id_plan = htmlspecialchars(strip_tags($this->id_plan));
        $stmt->bindParam(1, $this->id_plan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function planShow()
    {

        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    function showOne()
    {

        $query = " SELECT   p.asignatura,
                            p.clave,
                            p.tipo_asignatura,
                            p.ruta_pdf,
                            p.id_carrera,
                            c.nombre as nombre_carrera
                            FROM " . $this->table_name . " p 
                            INNER JOIN carreras c ON p.id_carrera = c.id_carrera
                            WHERE p.id_plan = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_plan);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->asignatura       = $row['asignatura'];
        $this->clave            = $row['clave'];
        $this->tipo_asignatura  = $row['tipo_asignatura'];
        $this->ruta_pdf         = $row['ruta_pdf'];
        $this->id_carrera       = $row['id_carrera'];
        $this->nombre_carrera   = $row['nombre_carrera'];
    }
}

<?php

class extensiones
{

    private $conn;
    private $table_name = "extensiones";

    public $id_ext;
    public $id_institucion;
    public $nombre_extesion;
    public $direccion;
    public $latitud;
    public $longitud;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function extensionCreate()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET id_institucion = :id_institucion,
                    nombre_extension = :nombre_extension.
                    direccion = :direccion,
                    latitud = :latitud,
                    longitud = :longitud
                   ";
        $stmt = $this->conn->prepare($query);

        $this->id_institucion = htmlspecialchars(strip_tags($this->id_institucion));
        $this->nombre_extension = htmlspecialchars(strip_tags($this->nombre_extension));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->latitud = htmlspecialchars(strip_tags($this->latitud));
        $this->longitud = htmlspecialchars(strip_tags($this->longitud));

        $stmt->bindParam(':id_institucion', $this->id_institucion);
        $stmt->bindParam(':nombre_extension', $this->nombre_extension);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':latitud', $this->latitud);
        $stmt->bindParam(':lontigut', $this->lontigut);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function extensionUpdate()
    {
        $query = "UPDATE " . $this->table_name . "
            SET id_institucion = :id_institucion,
                nombre_extension = :nombre_extension.
                direccion = :direccion,
                latitud = :latitud,
                longitud = :longitud
                WHERE id_ext = :id_ext";

        $stmt = $this->conn->prepare($query);

        $this->id_institucion = htmlspecialchars(strip_tags($this->id_institucion));
        $this->nombre_extension = htmlspecialchars(strip_tags($this->nombre_extension));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->latitud = htmlspecialchars(strip_tags($this->latitud));
        $this->longitud = htmlspecialchars(strip_tags($this->longitud));
        $this->id_ext = htmlspecialchars(strip_tags($this->id_ext));

        $stmt->bindParam(':id_institucion', $this->id_institucion);
        $stmt->bindParam(':nombre_extension', $this->nombre_extension);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':latitud', $this->latitud);
        $stmt->bindParam(':lontigut', $this->lontigut);

        $stmt->bindParam(':id_ext', $this->id_ext);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function extensionDelete()
    {

        $query = "DELETE FROM " . $this->table_name . " WHERE id_ext = ?";
        $stmt = $this->conn->prepare($query);

        $this->id_ext = htmlspecialchars(strip_tags($this->id_ext));

        $stmt->bindParam(1, $this->id_ext);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function extensionShow()
    {
        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    function showOne()
    {


        $query = " SELECT   
                            e.id_institucion,
                            i.nombre as nombre_institucion,
                            e.nombre_extesion,
                            e.direccion,
                            e.latitud,
                            e.longitud
                    FROM " . $this->table_name . " e
                            INNER JOIN instituciones i ON e.id_institucion = i.id_institucion
                            WHERE e.id_ext = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_ext);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id_institucion     = $row['id_institucion'];
        $this->nombre_institucion = $row['nombre_institucion'];
        $this->nombre_extesion    = $row['nombre_extesion'];
        $this->direccion          = $row['direccion'];
        $this->latitud            = $row['latitud'];
        $this->longitud           = $row['longitud'];
    }
}

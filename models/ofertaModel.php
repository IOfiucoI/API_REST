<?php

class oferta
{

    private $conn;
    private $table_name = "oferta_extension";

    public $id_ofe_ext;
    public $id_carrera;
    public $id_ext;
    public $cupo_x_carrera;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    function ofertaCreate()
    {

        $query = "INSERT INTO " . $this->table_name . "
                SET id_carrera = :id_carrera,
                    id_ext = :id_ext,
                    cupo_x_carrera = :cupo_x_carrera
                   ";
        $stmt = $this->conn->prepare($query);

        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));
        $this->id_ext = htmlspecialchars(strip_tags($this->id_ext));
        $this->cupo_x_carrera = htmlspecialchars(strip_tags($this->cupo_x_carrera));

        $stmt->bindParam(':id_carrera', $this->id_carrera);
        $stmt->bindParam(':id_ext', $this->id_ext);
        $stmt->bindParam(':cupo_x_carrera', $this->cupo_x_carrera);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function ofertaUpdate()
    {

        $query = "UPDATE " . $this->table_name . "
            SET id_carrera = :id_carrera,
                id_ext = :id_ext,
                cupo_x_carrera = :cupo_x_carrera
                WHERE id_ofe_ext = :id_ofe_ext";

        $stmt = $this->conn->prepare($query);

        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));
        $this->id_ext = htmlspecialchars(strip_tags($this->id_ext));
        $this->cupo_x_carrera = htmlspecialchars(strip_tags($this->cupo_x_carrera));
        $this->id_ofe_ext = htmlspecialchars(strip_tags($this->id_ofe_ext));

        $stmt->bindParam(':id_carrera', $this->id_carrera);
        $stmt->bindParam(':id_ext', $this->id_ext);
        $stmt->bindParam(':cupo_x_carrera', $this->cupo_x_carrera);
        $stmt->bindParam(':id_ofe_ext', $this->id_ofe_ext);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function ofertaDelete()
    {

        $query = "DELETE FROM " . $this->table_name . " WHERE id_ofe_ext = ?";
        $stmt = $this->conn->prepare($query);

        $this->id_ofe_ext = htmlspecialchars(strip_tags($this->id_ofe_ext));

        $stmt->bindParam(1, $this->id_ofe_ext);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function ofertaShow()
    {

        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function showOne()
    {

        $query = " SELECT   
                            o.id_carrera,
                            c.nombre as nombre_carrera,
                            o.id_ext,
                            e.nombre_extesion as nombre_extension,
                            o.cupo_x_carrera 
                   FROM " . $this->table_name . " o 
                            INNER JOIN carreras c ON o.id_carrera = c.id_carrera
                            INNER JOIN extensiones e ON o.id_ext = e.id_ext
                            WHERE o.id_ofe_ext = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_ofe_ext);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id_carrera           = $row['id_carrera'];
        $this->nombre_carrera       = $row['nombre_carrera'];
        $this->id_ext               = $row['id_ext'];
        $this->nombre_extension     = $row['nombre_extension'];
        $this->cupo_x_carrera       = $row['cupo_x_carrera'];
    }
}

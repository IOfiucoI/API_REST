<?php

class especialidad
{
    private $conn;
    private $table_name = "especialidad";

    public $id_especialidad;
    public $nombre;
    public $mision;
    public $vision;
    public $objetivo;
    public $id_oferta;
    public $carrerasid_carrera;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function createEspecialidad()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET id_especialidad = :id_especialidad,
                    nombre = :nombre,
                    mision = :mision,
                    vision = :vision,
                    objetivo = :objetivo,
                    id_oferta = :id_oferta,
                    carrerasid_carrera = :carrerasid_carrera
                   ";
        $stmt = $this->conn->prepare($query);

        $this->id_especialidad = htmlspecialchars(strip_tags($this->id_especialidad));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->mision = htmlspecialchars(strip_tags($this->mision));
        $this->vision = htmlspecialchars(strip_tags($this->vision));
        $this->objetivo = htmlspecialchars(strip_tags($this->objetivo));
        $this->id_oferta = htmlspecialchars(strip_tags($this->id_oferta));
        $this->carrerasid_carrera = htmlspecialchars(strip_tags($this->carrerasid_carrera));

        $stmt->bindParam(':id_especialidad', $this->id_especialidad);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':mision', $this->mision);
        $stmt->bindParam(':vision', $this->vision);
        $stmt->bindParam(':objetivo', $this->objetivo);
        $stmt->bindParam(':id_oferta', $this->id_oferta);
        $stmt->bindParam(':carrerasid_carrera', $this->carrerasid_carrera);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function especialidadUpdate()
    {
        $query = "UPDATE " . $this->table_name . "
            SET id_especialidad = :id_especialidad,
                nombre = :nombre,
                mision = :mision,
                vision = :vision,
                objetivo = :objetivo,
                id_oferta = :id_oferta,
                carrerasid_carrera = :carrerasid_carrera";

        $stmt = $this->conn->prepare($query);

        $this->id_especialidad = htmlspecialchars(strip_tags($this->id_especialidad));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->mision = htmlspecialchars(strip_tags($this->mision));
        $this->vision = htmlspecialchars(strip_tags($this->vision));
        $this->objetivo = htmlspecialchars(strip_tags($this->objetivo));
        $this->id_oferta = htmlspecialchars(strip_tags($this->id_oferta));
        $this->carrerasid_carrera = htmlspecialchars(strip_tags($this->carrerasid_carrera));

        $stmt->bindParam(':id_especialidad', $this->id_especialidad);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':mision', $this->mision);
        $stmt->bindParam(':vision', $this->vision);
        $stmt->bindParam(':objetivo', $this->objetivo);
        $stmt->bindParam(':id_oferta', $this->id_oferta);
        $stmt->bindParam(':carrerasid_carrera', $this->carrerasid_carrera);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function especialidadDelete()
    {

        $query = "DELETE FROM " . $this->table_name . " WHERE id_especialidad = ?";
        $stmt = $this->conn->prepare($query);
        $this->id_especialidad = htmlspecialchars(strip_tags($this->id_especialidad));
        $stmt->bindParam(1, $this->id_especialidad);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function especialidadShow()
    {
        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    function showOne()
    {

        $query = " SELECT   
                            e.nombre,
                            e.mision,
                            e.vision,
                            e.objetivo,
                            e.id_oferta,
                            e.carrerasid_carrera,
                            c.nombre AS nombre_carrera
                   FROM " . $this->table_name . " e  
                            INNER JOIN carreras c ON e.carrerasid_carrera = c.id_carrera 
                            WHERE e.id_especialidad = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_especialidad);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nombre                       = $row['nombre'];
        $this->mision                       = $row['mision'];
        $this->vision                       = $row['vision'];
        $this->objetivo                     = $row['objetivo'];
        $this->id_oferta                    = $row['id_oferta'];
        $this->carrerasid_carrera           = $row['carrerasid_carrera'];
        $this->nombre_carrera               = $row['nombre_carrera'];
    }
}

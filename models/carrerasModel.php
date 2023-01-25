<?php

class carreras
{
    private $conn;
    private $table_name = "carreras";

    public $id_carrera;
    public $nombre;
    public $resenia;
    public $objetivo;
    public $mision;
    public $vision;
    public $porque;
    public $perfil_ingreso;
    public $perfil_egreso;
    public $campo_laboral;
    public $institucionesid_institucion;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Funcion para crear usuarios
    function createCarrera()
    {

        $query = "INSERT INTO " . $this->table_name . "
                SET nombre = :nombre,
                    resenia = :resenia,
                    objetivo = :objetivo,
                    mision = :mision,
                    vision = :vision,
                    porque = :porque,
                    perfil_ingreso = :perfil_ingreso,
                    perfil_egreso = :perfil_egreso,
                    campo_laboral = :campo_laboral,
                    institucionesid_institucion = :institucionesid_institucion";

        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->resenia = htmlspecialchars(strip_tags($this->resenia));
        $this->objetivo = htmlspecialchars(strip_tags($this->objetivo));
        $this->mision = htmlspecialchars(strip_tags($this->mision));
        $this->vision = htmlspecialchars(strip_tags($this->vision));
        $this->porque = htmlspecialchars(strip_tags($this->porque));
        $this->perfil_ingreso = htmlspecialchars(strip_tags($this->perfil_ingreso));
        $this->perfil_egreso = htmlspecialchars(strip_tags($this->perfil_egreso));
        $this->campo_laboral = htmlspecialchars(strip_tags($this->campo_laboral));
        $this->institucionesid_institucion = htmlspecialchars(strip_tags($this->institucionesid_institucion));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':resenia', $this->resenia);
        $stmt->bindParam(':objetivo', $this->objetivo);
        $stmt->bindParam(':mision', $this->mision);
        $stmt->bindParam(':vision', $this->vision);
        $stmt->bindParam(':porque', $this->porque);
        $stmt->bindParam(':perfil_ingreso', $this->perfil_ingreso);
        $stmt->bindParam(':perfil_egreso', $this->perfil_egreso);
        $stmt->bindParam(':campo_laboral', $this->campo_laboral);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }



    function carreraUpdate()
    {

        $query = "UPDATE " . $this->table_name . "
                SET nombre = :nombre,
                    resenia = :resenia,
                    objetivo = :objetivo,
                    mision = :mision,
                    vision = :vision,
                    porque = :porque,
                    perfil_ingreso = :perfil_ingreso,
                    perfil_egreso = :perfil_egreso,
                    campo_laboral = :campo_laboral,
                    institucionesid_institucion = :institucionesid_institucion
                    WHERE id_carrera = :id_carrera";

        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->resenia = htmlspecialchars(strip_tags($this->resenia));
        $this->objetivo = htmlspecialchars(strip_tags($this->objetivo));
        $this->mision = htmlspecialchars(strip_tags($this->mision));
        $this->vision = htmlspecialchars(strip_tags($this->vision));
        $this->porque = htmlspecialchars(strip_tags($this->porque));
        $this->perfil_ingreso = htmlspecialchars(strip_tags($this->perfil_ingreso));
        $this->perfil_egreso = htmlspecialchars(strip_tags($this->perfil_egreso));
        $this->campo_laboral = htmlspecialchars(strip_tags($this->campo_laboral));
        $this->institucionesid_institucion = htmlspecialchars(strip_tags($this->institucionesid_institucion));
        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':resenia', $this->resenia);
        $stmt->bindParam(':objetivo', $this->objetivo);
        $stmt->bindParam(':mision', $this->mision);
        $stmt->bindParam(':vision', $this->vision);
        $stmt->bindParam(':porque', $this->porque);
        $stmt->bindParam(':perfil_ingreso', $this->perfil_ingreso);
        $stmt->bindParam(':perfil_egreso', $this->perfil_egreso);
        $stmt->bindParam(':campo_laboral', $this->campo_laboral);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);

        $stmt->bindParam(':id_carrera', $this->id_carrera);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function carreraDelete()
    {

        $query = "DELETE FROM " . $this->table_name . " WHERE id_carrera = ?";
        $stmt = $this->conn->prepare($query);
        $this->id_carrera = htmlspecialchars(strip_tags($this->id_carrera));
        $stmt->bindParam(1, $this->id_carrera);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    function carreraShow()
    {
        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    function showOne()
    {

        $query = " SELECT   c.nombre,
                            c.resenia,
                            c.objetivo,
                            c.mision,
                            c.vision,
                            c.porque,
                            c.perfil_ingreso,
                            c.perfil_egreso,
                            c.campo_laboral,
                            c.institucionesid_institucion,
                            i.nombre AS nombre_institucion 
                   FROM " . $this->table_name . " c  INNER JOIN instituciones i ON c.institucionesid_institucion = i.id_institucion WHERE c.id_carrera = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_carrera);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->nombre                       = $row['nombre'];
        $this->resenia                      = $row['resenia'];
        $this->objetivo                     = $row['objetivo'];
        $this->mision                       = $row['mision'];
        $this->vision                       = $row['vision'];
        $this->porque                       = $row['porque'];
        $this->perfil_ingreso               = $row['perfil_ingreso'];
        $this->perfil_egreso                = $row['perfil_egreso'];
        $this->campo_laboral                = $row['campo_laboral'];
        $this->institucionesid_institucion  = $row['institucionesid_institucion'];
        $this->nombre_institucion           = $row['nombre_institucion'];
    }
}

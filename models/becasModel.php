<?php

class becas
{

    private $conn;
    private $table_name = "becas";

    public $id_apoyos;
    public $id_institucion;
    public $programa;
    public $descripcion;
    public $periodo;
    public $documento;
    public $institucionesid_institucion;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    /**
     * createBeca
     *
     * @return void
     */
    function createBeca()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET 
                    id_institucion = :id_institucion,
                    programa = :programa,
                    descripcion = :descripcion,
                    periodo = :periodo,
                    documento = :documento,
                    institucionesid_institucion = :institucionesid_institucion                    
                    ";
        $stmt = $this->conn->prepare($query);

        $this->id_institucion               = htmlspecialchars(strip_tags($this->id_institucion));
        $this->programa                     = htmlspecialchars(strip_tags($this->programa));
        $this->descripcion                  = htmlspecialchars(strip_tags($this->descripcion));
        $this->periodo                      = htmlspecialchars(strip_tags($this->periodo));
        $this->documento                    = htmlspecialchars(strip_tags($this->documento));
        $this->institucionesid_institucion = htmlspecialchars(strip_tags($this->institucionesid_institucion));

        $stmt->bindParam(':id_institucion', $this->id_institucion);
        $stmt->bindParam(':programa', $this->programa);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':periodo', $this->periodo);
        $stmt->bindParam(':documento', $this->documento);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    /**
     * updateBeca
     *
     * @return void
     */
    function updateBeca()
    {
        $query = "UPDATE " . $this->table_name . "
        SET id_institucion = :id_institucion,
            programa = :programa,
            descripcion = :descripcion,
            periodo = :periodo,
            documento = :documento,
            institucionesid_institucion = :institucionesid_institucion
            WHERE id_apoyos = :id_apoyos";

        $stmt = $this->conn->prepare($query);

        $this->id_institucion               = htmlspecialchars(strip_tags($this->id_institucion));
        $this->programa                     = htmlspecialchars(strip_tags($this->programa));
        $this->descripcion                  = htmlspecialchars(strip_tags($this->descripcion));
        $this->periodo                      = htmlspecialchars(strip_tags($this->periodo));
        $this->documento                    = htmlspecialchars(strip_tags($this->documento));
        $this->institucionesid_institucion  = htmlspecialchars(strip_tags($this->institucionesid_institucion));
        $this->id_apoyos                    = htmlspecialchars(strip_tags($this->id_apoyos));

        $stmt->bindParam(':id_institucion', $this->id_institucion);
        $stmt->bindParam(':programa', $this->programa);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':periodo', $this->periodo);
        $stmt->bindParam(':documento', $this->documento);
        $stmt->bindParam(':institucionesid_institucion', $this->institucionesid_institucion);
        $stmt->bindParam(':id_apoyos', $this->id_apoyos);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * becaDelete
     *
     * @return void
     */
    function becaDelete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_apoyos = ?";
        $stmt = $this->conn->prepare($query);

        $this->id_apoyos = htmlspecialchars(strip_tags($this->id_apoyos));

        $stmt->bindParam(1, $this->id_apoyos);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * becaShow
     *
     * @return void
     */
    function becaShow()
    {

        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * showOne
     *
     * @return void
     */
    function showOne()
    {

        $query = " SELECT   b.id_institucion, 
                            b.programa, 
                            b.descripcion,
                            b.periodo,
                            b.documento, 
                            b.institucionesid_institucion,
                            i.nombre AS nombre_institucion 
                   FROM " . $this->table_name . " b  
                            INNER JOIN instituciones i ON b.institucionesid_institucion = i.id_institucion
                            WHERE b.id_apoyos = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_apoyos);
        $stmt->execute();

        // Muestra fila recuperada
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id_institucion               = $row['id_institucion'];
        $this->programa                     = $row['programa'];
        $this->descripcion                  = $row['descripcion'];
        $this->periodo                      = $row['periodo'];
        $this->documento                    = $row['documento'];
        $this->institucionesid_institucion  = $row['institucionesid_institucion'];
        $this->nombre_institucion           = $row['nombre_institucion'];
    }
}

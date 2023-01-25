<?php

class instituciones
{

    private $conn;
    private $table_name = 'instituciones';

    public $id_institucion;
    public $nombre;
    public $pagina_web;
    public $link_video_inst;
    public $direccion;
    public $localidad;
    public $municipio;
    public $modalidad;
    public $turno;
    public $telefono;
    public $correo_contacto;
    public $cupo_total;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    function createInstitucion()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET nombre          = :nombre,
                pagina_web      = :pagina_web,
                link_video_inst = :link_video_inst,
                direccion       = :direccion,
                localidad       = :localidad,
                municipio       = :municipio,
                modalidad       = :modalidad,
                turno           = :turno,
                telefono        = :telefono,
                correo_contacto = :correo_contacto,
                cupo_total      = :cupo_total                   
                    ";
        $stmt = $this->conn->prepare($query);

        $this->nombre           =   htmlspecialchars(strip_tags($this->nombre));
        $this->pagina_web       =   htmlspecialchars(strip_tags($this->pagina_web));
        $this->link_video_inst  =   htmlspecialchars(strip_tags($this->link_video_inst));
        $this->direccion        =   htmlspecialchars(strip_tags($this->direccion));
        $this->localidad        =   htmlspecialchars(strip_tags($this->localidad));
        $this->municipio        =   htmlspecialchars(strip_tags($this->municipio));
        $this->modalidad        =   htmlspecialchars(strip_tags($this->modalidad));
        $this->turno            =   htmlspecialchars(strip_tags($this->turno));
        $this->telefono         =   htmlspecialchars(strip_tags($this->telefono));
        $this->correo_contacto  =   htmlspecialchars(strip_tags($this->correo_contacto));
        $this->cupo_total       =   htmlspecialchars(strip_tags($this->cupo_total));

        $stmt->bindParam(':nombre',             $this->nombre);
        $stmt->bindParam(':pagina_web',         $this->pagina_web);
        $stmt->bindParam(':link_video_inst',    $this->link_video_inst);
        $stmt->bindParam(':direccion',          $this->direccion);
        $stmt->bindParam(':localidad',          $this->localidad);
        $stmt->bindParam(':municipio',          $this->municipio);
        $stmt->bindParam(':modalidad',          $this->modalidad);
        $stmt->bindParam(':turno',              $this->turno);
        $stmt->bindParam(':telefono',           $this->telefono);
        $stmt->bindParam(':correo_contacto',    $this->correo_contacto);
        $stmt->bindParam(':cupo_total',         $this->cupo_total);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function readOne()
    {
        $query = "SELECT    id_institucion,
                            nombre,
                            pagina_web,
                            link_video_inst,
                            direccion,
                            localidad,
                            municipio,
                            modalidad,
                            turno,
                            telefono,
                            correo_contacto,
                            cupo_total
                FROM " . $this->table_name . " WHERE id_institucion = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id_institucion   =   $row['id_institucion'];
        $this->nombre           =   $row['nombre'];
        $this->pagina_web       =   $row['pagina_web'];
        $this->link_video_inst  =   $row['link_video_inst'];
        $this->direccion        =   $row['direccion'];
        $this->localidad        =   $row['localidad'];
        $this->municipio        =   $row['municipio'];
        $this->modalidad        =   $row['modalidad'];
        $this->turno            =   $row['turno'];
        $this->telefono         =   $row['telefono'];
        $this->correo_contacto  =   $row['correo_contacto'];
        $this->cupo_total       =   $row['cupo_total'];
    }

    function readAll()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function updateInstitucion()
    {
        $query = "UPDATE " . $this->table_name . "
                SET
                nombre          = :nombre,
                pagina_web      = :pagina_web,
                link_video_inst = :link_video_inst,
                direccion       = :direccion,
                localidad       = :localidad,
                municipio       = :municipio,
                modalidad       = :modalidad,
                turno           = :turno,
                telefono        = :telefono,
                correo_contacto = :correo_contacto,
                cupo_total      = :cupo_total
                WHERE id_institucion = :id_institucion
                    ";
        $stmt = $this->conn->prepare($query);

        $this->id_institucion   =   htmlspecialchars(strip_tags($this->id_institucion));
        $this->nombre           =   htmlspecialchars(strip_tags($this->nombre));
        $this->pagina_web       =   htmlspecialchars(strip_tags($this->pagina_web));
        $this->link_video_inst  =   htmlspecialchars(strip_tags($this->link_video_inst));
        $this->direccion        =   htmlspecialchars(strip_tags($this->direccion));
        $this->localidad        =   htmlspecialchars(strip_tags($this->localidad));
        $this->municipio        =   htmlspecialchars(strip_tags($this->municipio));
        $this->modalidad        =   htmlspecialchars(strip_tags($this->modalidad));
        $this->turno            =   htmlspecialchars(strip_tags($this->turno));
        $this->telefono         =   htmlspecialchars(strip_tags($this->telefono));
        $this->correo_contacto  =   htmlspecialchars(strip_tags($this->correo_contacto));
        $this->cupo_total       =   htmlspecialchars(strip_tags($this->cupo_total));

        $stmt->bindParam(':id_institucion',     $this->id_institucion);
        $stmt->bindParam(':nombre',             $this->nombre);
        $stmt->bindParam(':pagina_web',         $this->pagina_web);
        $stmt->bindParam(':link_video_inst',    $this->link_video_inst);
        $stmt->bindParam(':direccion',          $this->direccion);
        $stmt->bindParam(':localidad',          $this->localidad);
        $stmt->bindParam(':municipio',          $this->municipio);
        $stmt->bindParam(':modalidad',          $this->modalidad);
        $stmt->bindParam(':turno',              $this->turno);
        $stmt->bindParam(':telefono',           $this->telefono);
        $stmt->bindParam(':correo_contacto',    $this->correo_contacto);
        $stmt->bindParam(':cupo_total',         $this->cupo_total);


        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function deleteInstitucion()
    {

        $query = "DELETE FROM " . $this->table_name . " WHERE id_institucion = ?";
        $stmt = $this->conn->prepare($query);

        $this->id_institucion = htmlspecialchars(strip_tags($this->id_institucion));
        $stmt->bindParam(1, $this->id_institucion);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

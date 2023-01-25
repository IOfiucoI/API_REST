<?php

class users
{

    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $id_institucion;
    public $correo;
    public $pass;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function createUser()
    {
        $query = "INSERT INTO " . $this->table_name . "
            SET id_institucion = :id_institucion,
                correo = :correo,
                pass = :pass";
        $stmt = $this->conn->prepare($query);

        // Verifica caracteres ingresados en formulario
        $this->id_institucion = htmlspecialchars(strip_tags($this->id_institucion));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->pass = htmlspecialchars(strip_tags($this->pass));

        // Convierte los datos ingresados del formulario a PDO
        $stmt->bindParam(':id_institucion', $this->id_institucion);
        $stmt->bindParam(':correo', $this->correo);

        // Cifra contraseña usando algoritmo HASH de PHP
        $password_hash = password_hash($this->pass, PASSWORD_BCRYPT);
        $stmt->bindParam(':pass', $password_hash);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verifica si el email existe para inciar sesión
    function emailExists()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE correo = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $stmt->bindParam(1, $this->correo);
        $stmt->execute();
        $num = $stmt->rowCount();

        // Si el email existe se asignan los valores del objeto e inicia sesion
        if ($num > 0) {
            // Se obtiene los valores de la tabla usuarios
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se asignan los valores al objeto usuario
            $this->id_usuario = $row['id_usuario'];
            $this->id_institucion = $row['id_institucion'];
            $this->pass  = $row['pass'];
            return true;
        }
        return false;
    }

    function updateUser()
    {
        $password_set = !empty($this->pass) ? ", pass = :pass" : "";
        $query = "UPDATE " . $this->table_name . "
                SET id_institucion = :id_institucion,
                    correo = :correo
                    {$password_set}
                WHERE id_usuario = :id_usuario";

        // Se prepara la consulta
        $stmt = $this->conn->prepare($query);
        $this->id_institucion = htmlspecialchars(strip_tags($this->id_institucion));
        $this->correo = htmlspecialchars(strip_tags($this->correo));

        $stmt->bindParam(':id_institucion', $this->id_institucion);
        $stmt->bindParam(':correo', $this->correo);

        // Se cifra el pass actualizado
        if (!empty($this->pass)) {
            $this->pass = htmlspecialchars(strip_tags($this->pass));
            $password_hash = password_hash($this->pass, PASSWORD_BCRYPT);
            $stmt->bindParam(':pass', $password_hash);
        }

        $stmt->bindParam(':id_usuario', $this->id_usuario);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function userDelete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);

        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        $stmt->bindParam(1, $this->id_usuario);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function userShow()
    {
        $query = "SELECT * FROM " . $this->table_name . "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function userShowOne()
    {

        $query = " SELECT   u.id_institucion,
                            i.nombre as nombre_institucion,
                            u.correo
                   FROM " . $this->table_name . " u 
                            INNER JOIN instituciones i ON u.id_institucion = i.id_institucion
                            WHERE u.id_usuario = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id_institucion       = $row['id_institucion'];
        $this->nombre_institucion   = $row['nombre_institucion'];
        $this->correo               = $row['correo'];
    }
}

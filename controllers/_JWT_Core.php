
<?php
// Muestra error
error_reporting(E_ALL);

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Variables requeridas para generar token
$key = "example_key";
$issued_at = time();
$expiration_time = $issued_at + (60 * 60);
$issuer = "http://localhost/REST_API/";
?>
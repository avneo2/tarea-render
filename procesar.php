<?php
// Datos sacados de las variables de entorno (Obligatorio para Render)
$host = getenv('DB_HOST');
$port = (int) getenv('DB_PORT');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricula = $conn->real_escape_string($_POST['matricula']);
    $carrera = $conn->real_escape_string($_POST['carrera']);
    $q1 = $conn->real_escape_string($_POST['q1_instalaciones']);
    $q2 = $conn->real_escape_string($_POST['q2_biblioteca']);
    $q3 = $conn->real_escape_string($_POST['q3_profesores']);
    $q4 = $conn->real_escape_string($_POST['q4_laboratorios']);
    $q5 = $conn->real_escape_string($_POST['q5_mejora']);

    $sql = "INSERT INTO respuestas (matricula, carrera, q1_instalaciones, q2_biblioteca, q3_profesores, q4_laboratorios, q5_mejora) 
            VALUES ('$matricula', '$carrera', '$q1', '$q2', '$q3', '$q4', '$q5')";

    if ($conn->query($sql) === TRUE) {
        header("Location: regalo.php");
        exit();
    } else {
        echo "Error en la consulta: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Tu usuario MySQL
define('DB_PASS', '');           // Tu contraseña MySQL
define('DB_NAME', 'datos_personales');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode([
        'status'  => 'error',
        'message' => 'Error de conexión: ' . $e->getMessage()
    ]));
}
?>

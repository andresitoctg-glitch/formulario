<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

// ── Sanitizar y validar entradas ──────────────────────────────────────────
function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

$nombre    = clean($_POST['nombre']    ?? '');
$apellido  = clean($_POST['apellido']  ?? '');
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$telefono  = clean($_POST['telefono']  ?? '');
$fecha_nac = clean($_POST['fecha_nac'] ?? '');
$genero    = clean($_POST['genero']    ?? '');
$direccion = clean($_POST['direccion'] ?? '');
$ciudad    = clean($_POST['ciudad']    ?? '');
$pais      = clean($_POST['pais']      ?? '');

// ── Validaciones del servidor ─────────────────────────────────────────────
$errors = [];

if (strlen($nombre) < 2)
    $errors[] = 'El nombre debe tener al menos 2 caracteres.';

if (strlen($apellido) < 2)
    $errors[] = 'El apellido debe tener al menos 2 caracteres.';

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'El correo electrónico no es válido.';

if (!in_array($genero, ['Masculino', 'Femenino', 'Otro']))
    $errors[] = 'El género seleccionado no es válido.';

if (!empty($fecha_nac) && !DateTime::createFromFormat('Y-m-d', $fecha_nac))
    $errors[] = 'La fecha de nacimiento no es válida.';

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

// ── Insertar en la base de datos ──────────────────────────────────────────
try {
    $sql = "INSERT INTO personas
                (nombre, apellido, email, telefono, fecha_nac, genero, direccion, ciudad, pais)
            VALUES
                (:nombre, :apellido, :email, :telefono, :fecha_nac, :genero, :direccion, :ciudad, :pais)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'    => $nombre,
        ':apellido'  => $apellido,
        ':email'     => $email,
        ':telefono'  => $telefono ?: null,
        ':fecha_nac' => $fecha_nac ?: null,
        ':genero'    => $genero,
        ':direccion' => $direccion ?: null,
        ':ciudad'    => $ciudad    ?: null,
        ':pais'      => $pais      ?: null,
    ]);

    echo json_encode([
        'status'  => 'success',
        'message' => "Registro guardado correctamente. ID: " . $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    // Email duplicado
    if ($e->getCode() === '23000') {
        echo json_encode([
            'status'  => 'error',
            'message' => 'El correo electrónico ya está registrado.'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al guardar: ' . $e->getMessage()
        ]);
    }
}
?>

<?php
// =======================
// CONFIGURACIÓN BD
// =======================
$host = getenv("DB_HOST");
$dbname = getenv("DB_NAME");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");

$message = "";

// =======================
// CONEXIÓN POSTGRESQL
// =======================
try {
    $pdo = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// =======================
// PROCESAR FORMULARIO
// =======================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = $_POST["nombre"];
    $atun     = intval($_POST["atun"]);
    $gallega  = intval($_POST["gallega"]);
    $carne    = intval($_POST["carne"]);
    $pisto    = intval($_POST["pisto"]);

    $sql = "INSERT INTO pedidos
            (nombre, atun, gallega, carne, pisto)
            VALUES (:nombre, :atun, :gallega, :carne, :pisto)";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ":nombre"  => $nombre,
            ":atun"    => $atun,
            ":gallega" => $gallega,
            ":carne"   => $carne,
            ":pisto"   => $pisto
        ]);

        $message = "✅ Pedido registrado correctamente";
    } catch (PDOException $e) {
        $message = "❌ Error al registrar el pedido";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de empanadillas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; margin-top: 10px; }
        td, th { border: 1px solid #333; padding: 6px 10px; }
        .msg { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>

<h1>Encargo de empanadillas</h1>

<form method="POST">
    <label>
        Nombre:
        <input type="text" name="nombre" required>
    </label>

    <table>
        <tr>
            <th>Tipo</th>
            <th>Cantidad</th>
        </tr>
        <tr>
            <td>Atún</td>
            <td><input type="number" name="atun" min="0" value="0"></td>
        </tr>
        <tr>
            <td>Gallega</td>
            <td><input type="number" name="gallega" min="0" value="0"></td>
        </tr>
        <tr>
            <td>Carne</td>
            <td><input type="number" name="carne" min="0" value="0"></td>
        </tr>
        <tr>
            <td>Pisto</td>
            <td><input type="number" name="pisto" min="0" value="0"></td>
        </tr>
    </table>

    <br>
    <button type="submit">Encargar</button>
</form>

<?php if ($message): ?>
    <div class="msg"><?= $message ?></div>
<?php endif; ?>

</body>
</html>

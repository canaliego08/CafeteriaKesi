<?php
// =======================
// CONEXIÓN BD
// =======================
$pdo = new PDO(
    "pgsql:host=" . getenv("DB_HOST") . ";dbname=" . getenv("DB_NAME"),
    getenv("DB_USER"),
    getenv("DB_PASSWORD"),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// =======================
// PROCESAR PEDIDO
// =======================
$pedido_realizado = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stmt = $pdo->prepare("
        INSERT INTO pedidos (nombre, atun, gallega, carne, pisto, fecha_pedido, vendido)
        VALUES (:nombre, :atun, :gallega, :carne, :pisto, NOW(), false)
    ");

    $stmt->execute([
        ":nombre"   => $_POST["nombre"],
        ":atun"     => (int)($_POST["atun"] ?? 0),
        ":gallega"  => (int)($_POST["gallega"] ?? 0),
        ":carne"    => (int)($_POST["carne"] ?? 0),
        ":pisto"    => (int)($_POST["pisto"] ?? 0),
    ]);

    $pedido_realizado = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pedido de empanadillas</title>

<?php if ($pedido_realizado): ?>
<meta http-equiv="refresh" content="4;url=index.php">
<?php endif; ?>

<style>
body {
    font-family: system-ui, sans-serif;
    background: #f3f4f6;
    padding: 1rem;
}

.container {
    max-width: 500px;
    margin: auto;
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
}

h1 {
    text-align: center;
    margin-bottom: 1rem;
}

label {
    display: block;
    margin-bottom: 0.8rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

td {
    padding: 0.5rem;
    text-align: center;
}

input[type="number"] {
    width: 60px;
}

button {
    width: 100%;
    padding: 0.8rem;
    margin-top: 1.2rem;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    background: #2563eb;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #1d4ed8;
}

.confirmacion {
    text-align: center;
    padding: 2rem 1rem;
}

.confirmacion h1 {
    color: #16a34a;
}

.confirmacion .emoji {
    font-size: 3.5rem;
    margin: 1rem 0;
}
</style>
</head>

<body>

<div class="container">

<?php if ($pedido_realizado): ?>

    <!-- CONFIRMACIÓN -->
    <div class="confirmacion">
        <h1>¡Pedido realizado!</h1>
        <div class="emoji">😊</div>
        <p>Gracias por tu pedido</p>
        <p>Volviendo al formulario…</p>
    </div>

<?php else: ?>

    <!-- FORMULARIO ORIGINAL -->
    <h1>📝 Pedido</h1>

    <form method="post">
        <label>
            Nombre:
            <input type="text" name="nombre" required>
        </label>

        <table>
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

        <button type="submit">Encargar</button>
    </form>

<?php endif; ?>

</div>

</body>
</html>

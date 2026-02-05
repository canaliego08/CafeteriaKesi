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
    <!-- Redirección automática -->
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
}

table {
    width: 100%;
    margin-top: 1rem;
}

td {
    padding: 0.4rem;
    text-align: center;
}

input[type="number"], input[type="text"] {
    width: 60px;
}

button {
    width: 100%;
    padding: 0.8rem;
    margin-top: 1rem;
    font-size: 1.1rem;
    border: none;
    border-radius: 8px;
    background: #22c55e;
    color: white;
    cursor: pointer;
}

.confirmacion {
    text-align: center;
    padding: 2rem 1rem;
}

.confirmacion h1 {
    font-size: 2rem;
    color: #16a34a;
}

.confirmacion .emoji {
    font-size: 4rem;
    margin-top: 1rem;
}
</style>
</head>

<body>

<?php if ($pedido_realizado): ?>

    <!-- PANTALLA CONFIRMACIÓN -->
    <div class="container confirmacion">
        <h1>¡Pedido realizado!</h1>
        <div class="emoji">😊</div>
        <p>Gracias por tu pedido</p>
        <p>Volviendo al formulario...</p>
    </div>

<?php else: ?>

    <!-- FORMULARIO -->
    <div class="container">
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
    </div>

<?php endif; ?>

</body>
</html>





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
// ACTUALIZAR VENDIDO
// =======================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $stmt = $pdo->prepare("UPDATE pedidos SET vendido = :vendido WHERE id = :id");
    $stmt->execute([
        ":vendido" => isset($_POST["vendido"]),
        ":id" => $_POST["id"]
    ]);
}

// =======================
// FILTROS
// =======================
$desde = $_GET["desde"] ?? date("Y-m-d");
$hasta = $_GET["hasta"] ?? date("Y-m-d");
$estado = $_GET["estado"] ?? "todos";

$where = "fecha_pedido BETWEEN :desde AND :hasta";
$params = [
    ":desde" => $desde . " 00:00:00",
    ":hasta" => $hasta . " 23:59:59"
];

if ($estado === "vendidos") {
    $where .= " AND vendido = true";
} elseif ($estado === "pendientes") {
    $where .= " AND vendido = false";
}

// =======================
// CONSULTA PEDIDOS
// =======================
$sql = "SELECT id, nombre, atun, gallega, carne, pisto, vendido
        FROM pedidos
        WHERE $where
        ORDER BY fecha_pedido DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =======================
// TOTALES
// =======================
$sqlTotales = "SELECT
    COALESCE(SUM(atun),0) AS atun,
    COALESCE(SUM(gallega),0) AS gallega,
    COALESCE(SUM(carne),0) AS carne,
    COALESCE(SUM(pisto),0) AS pisto
    FROM pedidos
    WHERE $where";

$stmt = $pdo->prepare($sqlTotales);
$stmt->execute($params);
$totales = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Consultas</title>

<style>
body {
    font-family: system-ui, sans-serif;
    background: #f3f4f6;
    padding: 1rem;
}

h1 { text-align: center; }

.totales {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.total {
    background: white;
    padding: 0.8rem;
    border-radius: 10px;
    text-align: center;
    font-weight: bold;
}

.filtros, table {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.filtros {
    display: grid;
    gap: 0.8rem;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 0.4rem;
    border-bottom: 1px solid #e5e7eb;
    text-align: center;
}
</style>
</head>

<body>

<h1>📋 Pedidos</h1>

<!-- TOTALES -->
<div class="totales">
    <div class="total">Atún<br><?= $totales["atun"] ?></div>
    <div class="total">Gallega<br><?= $totales["gallega"] ?></div>
    <div class="total">Carne<br><?= $totales["carne"] ?></div>
    <div class="total">Pisto<br><?= $totales["pisto"] ?></div>
</div>

<!-- FILTROS -->
<form class="filtros" method="GET">
    <label>Desde
        <input type="date" name="desde" value="<?= $desde ?>">
    </label>

    <label>Hasta
        <input type="date" name="hasta" value="<?= $hasta ?>">
    </label>

    <label>Estado
        <select name="estado">
            <option value="todos" <?= $estado=="todos"?"selected":"" ?>>Todos</option>
            <option value="vendidos" <?= $estado=="vendidos"?"selected":"" ?>>Vendidos</option>
            <option value="pendientes" <?= $estado=="pendientes"?"selected":"" ?>>Pendientes</option>
        </select>
    </label>

    <button>Filtrar</button>
</form>

<!-- TABLA -->
<table>
<tr>
    <th>Nombre</th>
    <th>Atún</th>
    <th>Gallega</th>
    <th>Carne</th>
    <th>Pisto</th>
    <th>Vendido</th>
</tr>

<?php foreach ($pedidos as $p): ?>
<tr>
    <td><?= htmlspecialchars($p["nombre"]) ?></td>
    <td><?= $p["atun"] ?></td>
    <td><?= $p["gallega"] ?></td>
    <td><?= $p["carne"] ?></td>
    <td><?= $p["pisto"] ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $p["id"] ?>">
            <input type="checkbox" name="vendido" onchange="this.form.submit()"
                <?= $p["vendido"] ? "checked" : "" ?>>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>

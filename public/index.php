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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de empanadillas</title>

    <style>
        :root {
            --color-principal: #7c3aed;
            --color-fondo: #f5f5f5;
            --color-card: #ffffff;
            --color-borde: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: var(--color-fondo);
            padding: 1rem;
        }

        .container {
            max-width: 420px;
            margin: auto;
        }

        .card {
            background: var(--color-card);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            text-align: center;
            color: var(--color-principal);
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 0.6rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid var(--color-borde);
            margin-bottom: 1rem;
        }

        .productos {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .producto {
            display: contents;
        }

        .producto span {
            align-self: center;
        }

        button {
            width: 100%;
            background-color: var(--color-principal);
            color: white;
            border: none;
            padding: 0.8rem;
            font-size: 1.1rem;
            border-radius: 10px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .msg {
            margin-top: 1rem;
            padding: 0.8rem;
            border-radius: 8px;
            text-align: center;
            background-color: #ecfdf5;
            color: #065f46;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>🥟 Pedido</h1>

        <form method="POST">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>

            <div class="productos">
                <div class="producto">
                    <span>Atún</span>
                    <input type="number" name="atun" min="0" value="0">
                </div>

                <div class="producto">
                    <span>Gallega</span>
                    <input type="number" name="gallega" min="0" value="0">
                </div>

                <div class="producto">
                    <span>Carne</span>
                    <input type="number" name="carne" min="0" value="0">
                </div>

                <div class="producto">
                    <span>Pisto</span>
                    <input type="number" name="pisto" min="0" value="0">
                </div>
            </div>

            <button type="submit">Encargar</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>



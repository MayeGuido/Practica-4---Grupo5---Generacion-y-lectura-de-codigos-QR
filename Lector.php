<?php
session_start();

if (!isset($_SESSION["historial"])) {
    $_SESSION["historial"] = [];
}

if (isset($_POST["resetHistorial"])) {
    $_SESSION["historial"] = [];
    header("Location: lector.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["qrData"])) {
    $qr = $_POST["qrData"];

    $_SESSION["historial"][] = [
        "data" => $qr,
        "timestamp" => date("Y-m-d H:i:s")
    ];

    if (filter_var($qr, FILTER_VALIDATE_URL)) {
        echo "<script>window.open('$qr', '_blank');</script>";
    } else {
        $safeText = htmlspecialchars($qr, ENT_QUOTES);
        echo "<script>
            let win = window.open('', '_blank');
            win.document.write(\"<pre style='font-size:18px; white-space:pre-wrap;'>$safeText</pre>\");
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lector QR PHP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <style>
        video {
            width: 100%;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container d-flex justify-content-between">

        <a href="menuPrin.html" class="navbar-brand fw-bold text-white">
            QR App
        </a>

        <a href="generador.php" class="text-white text-decoration-none fw-semibold">
            Ir al Generador
        </a>
    </div>
</nav>

<div class="container py-5">

    <div class="card shadow mx-auto" style="max-width: 700px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Lector de Códigos QR</h5>
        </div>

        <div class="card-body">

            <video id="video" autoplay muted></video>
            <canvas id="canvas" class="d-none"></canvas>

            <div class="alert alert-info mt-3" id="result">
                Apunta la cámara a un código QR.
            </div>

            <form method="POST" id="redirectForm">
                <input type="hidden" name="qrData" id="qrData">
            </form>

            <form method="POST" class="mt-3">
                <button class="btn btn-danger w-100" name="resetHistorial">
                    Reiniciar historial
                </button>
            </form>

            <h5 class="mt-4">Historial</h5>

            <ul class="list-group mb-3">
                <?php foreach ($_SESSION["historial"] as $item): ?>
                    <?php
                        $data = htmlspecialchars($item["data"]);
                        $time = htmlspecialchars($item["timestamp"]);
                        $esURL = filter_var($item["data"], FILTER_VALIDATE_URL);
                    ?>
                    <li class="list-group-item">
                        <?php if ($esURL): ?>
                            <a href="<?= $data ?>" target="_blank">🌐 <?= $data ?></a>
                        <?php else: ?>
                            <a href="#" onclick="openText('<?= $data ?>')">📝 <?= $data ?></a>
                        <?php endif; ?>
                        <br>
                        <small class="text-muted"><?= $time ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </div>

</div>

<script>
const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
const resultElement = document.getElementById("result");
const qrInput = document.getElementById("qrData");
const form = document.getElementById("redirectForm");

navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then(stream => {
        video.srcObject = stream;
        scanQR();
    })
    .catch(err => {
        resultElement.className = "alert alert-danger";
        resultElement.textContent = "Error al acceder a la cámara: " + err.message;
    });

function scanQR() {
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(img.data, img.width, img.height);

        if (code) {
            resultElement.className = "alert alert-success";
            resultElement.textContent = "Código detectado: " + code.data;

            qrInput.value = code.data;
            form.submit();
            return;
        }
    }
    requestAnimationFrame(scanQR);
}

function openText(texto) {
    let win = window.open("", "_blank");
    win.document.write("<pre style='font-size:18px; white-space:pre-wrap;'>" + texto + "</pre>");
}
</script>

</body>
</html>

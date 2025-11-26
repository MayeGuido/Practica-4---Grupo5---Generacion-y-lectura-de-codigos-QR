<?php
require_once "phpqrcode/qrlib.php";

// Carpeta donde se guardarán los QR
$qrDir = "qr_codes/";
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}

if (empty($_POST["qr_text"])) {
    die("No ingresaste texto para generar el QR.");
}

$data = $_POST["qr_text"];

// Nombre del archivo QR
$qrFile = $qrDir . "qr_" . time() . ".png";

// Generar QR
QRcode::png($data, $qrFile, QR_ECLEVEL_L, 10, 2);

// Redirigir para mostrar el QR generado
header("Location: generador.php?file=" . $qrFile);
exit;
?>

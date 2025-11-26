<?php
require_once 'phpqrcode/qrlib.php'; // Incluye la librería

class QRGenerator {
    private $uploadDir = 'uploads/';

    public function __construct() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function generate($text = null, $imageFile = null) {
        $data = $this->prepareData($text, $imageFile);
        // Genera el QR en memoria y lo envía como JPG
        ob_start(); // Inicia buffer de salida
        QRcode::png($data, null, QR_ECLEVEL_L, 10); // Genera PNG en buffer
        $pngData = ob_get_contents(); // Obtiene el PNG
        ob_end_clean(); // Limpia buffer

        // Convierte PNG a JPG
        $image = imagecreatefromstring($pngData);
        header('Content-Type: image/jpeg');
        imagejpeg($image); // Envía como JPG
        imagedestroy($image); // Libera memoria
    }

    private function prepareData($text, $imageFile) {
        if ($text && !empty($text)) {
            return $text;
        } elseif ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            return $this->uploadImage($imageFile);
        }
        return 'https://example.com'; // Fallback
    }

    private function uploadImage($file) {
        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $this->uploadDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return 'https://' . $_SERVER['HTTP_HOST'] . '/' . $filePath; // URL completa
        }
        throw new Exception('Error al subir la imagen.');
    }
}

// Uso de la clase
$generator = new QRGenerator();
try {
    $generator->generate($_POST['qr_text'] ?? null, $_FILES['qr_image'] ?? null);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
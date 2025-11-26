<?php
require_once 'phpqrcode/qrlib.php';

class QRGenerator {
    private $uploadDir = 'uploads/';

    public function __construct() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function generate($text = null, $imageFile = null) {
        $data = $this->prepareData($text, $imageFile);
        ob_start();
        QRcode::png($data, null, QR_ECLEVEL_L, 10);
        $pngData = ob_get_contents();
        ob_end_clean();

        $image = imagecreatefromstring($pngData);
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
    }

    private function prepareData($text, $imageFile) {
        if ($text && !empty($text)) {
            return $text;
        } elseif ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            return $this->uploadImage($imageFile);
        }
        return 'https://example.com';
    }

    private function uploadImage($file) {
        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $this->uploadDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return 'https://' . $_SERVER['HTTP_HOST'] . '/' . $filePath;
        }
        throw new Exception('Error al subir la imagen.');
    }
}

$generator = new QRGenerator();
try {
    $generator->generate($_POST['qr_text'] ?? null, $_FILES['qr_image'] ?? null);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
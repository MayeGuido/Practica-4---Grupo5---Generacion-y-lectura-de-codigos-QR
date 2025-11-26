<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de QR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        
        <a href="menuPrin.html" class="navbar-brand fw-bold">
            QR App
        </a>

        <div class="ms-auto">
            <a class="nav-link text-white fw-semibold" href="lector.php">
                Ir al Lector
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow">

                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Generador de Códigos QR</h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="generate_qr.php">

                        <div class="mb-3">
                            <label class="form-label">Texto o URL:</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="qr_text" 
                                required 
                                placeholder="https://example.com"
                            >
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            Generar QR
                        </button>
                    </form>

                    <?php if (isset($_GET["file"])): ?>
                        <div class="mt-4 text-center">
                            <h6 class="mb-3">Código QR generado:</h6>

                            <div class="d-flex flex-column align-items-center">
                                <img 
                                    src="<?= htmlspecialchars($_GET["file"]) ?>" 
                                    class="img-fluid border p-2 bg-white shadow-sm mb-3"
                                    style="max-width: 300px;"
                                >

                                <a 
                                    href="<?= htmlspecialchars($_GET["file"]) ?>" 
                                    download="qr_code.png"
                                    class="btn btn-outline-primary"
                                >
                                    Descargar QR
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>
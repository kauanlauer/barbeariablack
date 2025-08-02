<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectar WhatsApp - The Black Beard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">

    <!-- Menu de Navegação -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-black bg-gradient">
        <div class="container-fluid">
            <a class="navbar-brand text-warning" href="index.php" style="font-family: 'Georgia', serif;">
                <i class="bi bi-scissors"></i>
                The Black Beard Barbershop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-grid-1x2-fill"></i> Painel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="leitor_qr.php"><i class="bi bi-qr-code-scan"></i> Conectar WhatsApp</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 90vh;">
        <div class="bg-dark p-4 p-md-5 rounded-3 shadow-lg text-center border border-secondary">
            <h1 class="h3 mb-3">Conectar ao WhatsApp</h1>
            <p class="text-muted mb-4">Abra o WhatsApp no celular da barbearia e escaneie o código abaixo.</p>
            
            <div id="qr-code-image" class="mb-4 bg-light p-3 rounded d-flex align-items-center justify-content-center" style="min-height: 280px; min-width: 280px;">
                <!-- O QR Code aparecerá aqui -->
                <div class="spinner-border text-dark" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Aguardando QR Code...</span>
                </div>
            </div>

            <div id="status-message" class="alert alert-info" role="alert">
                Aguardando conexão com o servidor do bot...
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qrCodeDiv = document.getElementById('qr-code-image');
            const statusMessageDiv = document.getElementById('status-message');
            const websocketPort = 8080; // A mesma porta definida no bot.js

            function connect() {
                const ws = new WebSocket(`ws://localhost:${websocketPort}`);

                ws.onopen = () => {
                    statusMessageDiv.className = 'alert alert-primary';
                    statusMessageDiv.innerHTML = '<i class="bi bi-info-circle-fill"></i> Aguardando o QR Code do bot...';
                };

                ws.onmessage = (event) => {
                    const message = JSON.parse(event.data);

                    if (message.type === 'qrcode') {
                        qrCodeDiv.innerHTML = `<img src="${message.data}" alt="QR Code do WhatsApp" />`;
                        statusMessageDiv.className = 'alert alert-primary';
                        statusMessageDiv.innerHTML = '<i class="bi bi-camera-fill"></i> Escaneie o código acima.';
                    } else if (message.type === 'status') {
                        qrCodeDiv.innerHTML = '<i class="bi bi-check-circle-fill text-success" style="font-size: 8rem;"></i>';
                        statusMessageDiv.className = 'alert alert-success';
                        statusMessageDiv.textContent = message.message;
                        if(message.message.includes('desconectado')) {
                            setTimeout(() => window.location.reload(), 5000);
                        }
                    }
                };

                ws.onclose = () => {
                    statusMessageDiv.className = 'alert alert-danger';
                    statusMessageDiv.textContent = 'Conexão perdida. Tentando reconectar...';
                    setTimeout(connect, 5000);
                };

                ws.onerror = (error) => {
                    statusMessageDiv.className = 'alert alert-danger';
                    statusMessageDiv.textContent = 'Erro de conexão. Verifique se o bot está rodando.';
                };
            }

            connect();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

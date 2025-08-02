<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - The Black Beard Barbershop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Teko:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light text-dark">

    <!-- Menu de Navegação -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php" style="font-family: 'Teko', sans-serif; font-size: 1.8rem; color: #343a40;">
                <i class="bi bi-scissors text-primary"></i>
                The Black Beard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php"><i class="bi bi-grid-1x2-fill"></i> Painel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="leitor_qr.php"><i class="bi bi-qr-code-scan"></i> Conectar WhatsApp</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container-fluid p-4">
        <main>
            <h2 class="h4 mb-3 text-secondary-emphasis"><i class="bi bi-calendar-week text-primary"></i> Próximos Agendamentos</h2>
            
            <div class="table-responsive bg-white rounded shadow-sm">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-primary">
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th>Serviço</th>
                            <th>Duração</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-agendamentos">
                        <!-- Os agendamentos serão inseridos aqui pelo JavaScript -->
                    </tbody>
                </table>
            </div>
            <div id="sem-agendamentos" class="text-center text-muted p-5 bg-white rounded shadow-sm d-none">
                <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">Nenhum agendamento futuro encontrado.</p>
            </div>
        </main>
    </div>

    <!-- Modal para Ativação do Som -->
    <div class="modal fade" id="soundActivationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-volume-up-fill text-primary" style="font-size: 3rem;"></i>
                    <h5 class="modal-title mt-3">Ativar Notificações Sonoras</h5>
                    <p class="text-muted mt-2">Clique em qualquer lugar para iniciar o painel e habilitar os alertas de novos agendamentos.</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Iniciar Painel</button>
                </div>
            </div>
        </div>
    </div>

    <audio id="notification-sound" src="assets/sounds/notification.mp3" preload="auto"></audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>

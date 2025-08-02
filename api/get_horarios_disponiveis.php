<?php
// api/get_horarios_disponiveis.php
// Este script calcula e retorna os horários livres com base na duração de um serviço.

header("Content-Type: application/json; charset=UTF-8");

include_once 'db_connect.php';
include_once 'classes/Agendamento.php';

// --- Configurações da Barbearia ---
define('HORA_INICIO_TRABALHO', 9);  // 9h da manhã
define('HORA_FIM_TRABALHO', 18);    // 18h (6 da tarde)
define('INTERVALO_MINUTOS', 15);    // Verifica horários a cada 15 minutos

// Pega a duração do serviço enviada pelo bot (via GET)
$duracaoServico = isset($_GET['duracao']) ? (int)$_GET['duracao'] : 30;

// Conexão com o banco
$database = new Database();
$db = $database->getConnection();
$agendamento = new Agendamento($db);

// Busca todos os agendamentos futuros
$stmt = $agendamento->getAgendamentosPorPeriodo('todos');
$agendamentosOcupados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$horariosDisponiveis = [];
$dataAtual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
$dataFimBusca = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->modify('+7 days');

// Loop pelos próximos 7 dias
while ($dataAtual <= $dataFimBusca) {
    // Pula os domingos
    if ($dataAtual->format('N') == 7) {
        $dataAtual->modify('+1 day')->setTime(HORA_INICIO_TRABALHO, 0, 0);
        continue;
    }

    // Define o início e o fim do dia de trabalho
    $inicioDiaTrabalho = (clone $dataAtual)->setTime(HORA_INICIO_TRABALHO, 0, 0);
    $fimDiaTrabalho = (clone $dataAtual)->setTime(HORA_FIM_TRABALHO, 0, 0);

    // Loop pelos horários do dia, de 15 em 15 minutos
    $slotAtual = clone $inicioDiaTrabalho;
    while ($slotAtual < $fimDiaTrabalho) {
        $slotFim = (clone $slotAtual)->modify("+$duracaoServico minutes");

        // Verifica se o horário já passou ou se termina depois do expediente
        if ($slotAtual < new DateTime('now', new DateTimeZone('America/Sao_Paulo')) || $slotFim > $fimDiaTrabalho) {
            $slotAtual->modify("+" . INTERVALO_MINUTOS . " minutes");
            continue;
        }

        // Verifica se o slot está livre
        $estaDisponivel = true;
        foreach ($agendamentosOcupados as $agOcupado) {
            $agInicio = new DateTime($agOcupado['data_hora']);
            $agFim = (clone $agInicio)->modify("+" . $agOcupado['servico_duracao'] . " minutes");

            // Lógica para checar sobreposição de horários
            if ($slotAtual < $agFim && $slotFim > $agInicio) {
                $estaDisponivel = false;
                break; // Se encontrou conflito, não precisa checar os outros
            }
        }

        // Se, após todas as checagens, o horário estiver livre, adiciona à lista
        if ($estaDisponivel) {
            // Adiciona o horário no formato ISO 8601, que é universal
            $horariosDisponiveis[] = $slotAtual->format('Y-m-d\TH:i:s');
        }

        // Vai para o próximo slot de 15 minutos
        $slotAtual->modify("+" . INTERVALO_MINUTOS . " minutes");
    }

    // Avança para o próximo dia
    $dataAtual->modify('+1 day')->setTime(HORA_INICIO_TRABALHO, 0, 0);
}

// Retorna a lista de horários disponíveis em JSON
http_response_code(200);
echo json_encode($horariosDisponiveis);

?>

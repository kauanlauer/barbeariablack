<?php
// api/get_agendamentos.php
header("Content-Type: application/json; charset=UTF-8");

include_once 'db_connect.php';
include_once 'classes/Agendamento.php';

$database = new Database();
$db = $database->getConnection();
$agendamento = new Agendamento($db);

// ALTERAÇÃO AQUI: Mudamos de 'hoje' para 'todos' para buscar todos os agendamentos futuros.
$stmt = $agendamento->getAgendamentosPorPeriodo('todos');
$num = $stmt->rowCount();

if ($num > 0) {
    $agendamentos_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $agendamento_item = array(
            "id" => $id,
            "nome_cliente" => $nome_cliente,
            "telefone_cliente" => $telefone_cliente,
            "servico_nome" => $servico_nome,
            "servico_duracao" => $servico_duracao,
            // Adicionamos a data completa à resposta
            "data_completa" => date("d/m/Y", strtotime($data_hora)),
            "hora" => date("H:i", strtotime($data_hora)),
            "status" => $status
        );
        array_push($agendamentos_arr, $agendamento_item);
    }
    echo json_encode($agendamentos_arr);
} else {
    echo json_encode(array());
}
?>

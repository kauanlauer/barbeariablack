<?php
// api/criar_agendamento.php
// Este script recebe os dados do bot e cria um novo agendamento no banco.

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once 'db_connect.php';
include_once 'classes/Agendamento.php';

$database = new Database();
$db = $database->getConnection();
$agendamento = new Agendamento($db);

// Pega os dados enviados pelo bot no corpo da requisição
$data = json_decode(file_get_contents("php://input"));

// Verifica se todos os dados necessários foram enviados
if (
    !empty($data->nome_cliente) &&
    !empty($data->telefone_cliente) &&
    !empty($data->servico_nome) &&
    !empty($data->servico_duracao) &&
    !empty($data->data_hora)
) {
    // Define os valores do objeto agendamento
    $agendamento->nome_cliente = $data->nome_cliente;
    $agendamento->telefone_cliente = $data->telefone_cliente;
    $agendamento->servico_nome = $data->servico_nome;
    $agendamento->servico_duracao = $data->servico_duracao;
    $agendamento->data_hora = $data->data_hora;

    // Tenta criar o agendamento
    if ($agendamento->criar()) {
        // Se conseguir, retorna 201 (Criado)
        http_response_code(201);
        echo json_encode(array("message" => "Agendamento criado com sucesso."));
    } else {
        // Se não conseguir, retorna 503 (Serviço Indisponível)
        http_response_code(503);
        echo json_encode(array("message" => "Não foi possível criar o agendamento."));
    }
} else {
    // Se os dados estiverem incompletos, retorna 400 (Requisição Inválida)
    http_response_code(400);
    echo json_encode(array("message" => "Não foi possível criar o agendamento. Dados incompletos."));
}
?>

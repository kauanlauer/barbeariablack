<?php
// api/get_servicos.php
// Este script busca todos os serviços no banco de dados e os retorna em formato JSON.

// Define o cabeçalho da resposta como JSON
header("Content-Type: application/json; charset=UTF-8");

// Inclui o arquivo de conexão com o banco de dados
include_once 'db_connect.php';

// Cria uma nova instância da classe Database para obter a conexão
$database = new Database();
$db = $database->getConnection();

// Prepara a consulta SQL para selecionar todos os serviços
$query = "SELECT id, nome, duracao, valor FROM servicos ORDER BY nome ASC";
$stmt = $db->prepare($query);

// Executa a consulta
$stmt->execute();

// Pega o número de linhas retornadas
$num = $stmt->rowCount();

// Verifica se encontrou algum serviço
if ($num > 0) {
    // Cria um array para armazenar os serviços
    $servicos_arr = array();

    // Percorre todos os resultados da consulta
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Extrai as variáveis da linha ($id, $nome, etc.)
        extract($row);
        // Cria um item de serviço
        $servico_item = array(
            "id" => $id,
            "nome" => $nome,
            "duracao" => $duracao,
            "valor" => $valor
        );
        // Adiciona o item ao array de serviços
        array_push($servicos_arr, $servico_item);
    }

    // Define o código de resposta como 200 (OK)
    http_response_code(200);
    // Converte o array de serviços para JSON e o envia como resposta
    echo json_encode($servicos_arr);
} else {
    // Se não encontrar serviços, define o código como 404 (Não Encontrado)
    http_response_code(404);
    // Envia uma mensagem de erro em JSON
    echo json_encode(
        array("message" => "Nenhum serviço encontrado.")
    );
}
?>

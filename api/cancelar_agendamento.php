<?php
// api/cancelar_agendamento.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once 'db_connect.php';
include_once 'classes/Agendamento.php';

$database = new Database();
$db = $database->getConnection();
$agendamento = new Agendamento($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->telefone) && !empty($data->motivo)) {
    $agendamento->id = $data->id;

    // 1. Tenta cancelar no banco de dados
    if ($agendamento->cancelar()) {
        
        // 2. Se conseguiu, prepara para enviar a mensagem via WhatsApp
        $telefoneCompleto = "55" . preg_replace('/\D/', '', $data->telefone) . "@c.us";
        $mensagem = "Olá! Gostaríamos de informar que seu agendamento na The Black Beard Barbershop foi cancelado. Motivo: " . $data->motivo;

        // 3. Envia a requisição para a API do bot
        $ch = curl_init('http://localhost:8081/send-message'); // A porta da API do bot
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'to' => $telefoneCompleto,
            'text' => $mensagem
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        // ADICIONADO: Define um tempo limite de 5 segundos para a conexão.
        // Se o bot não responder nesse tempo, a requisição falha.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch); // Pega a mensagem de erro da conexão, se houver
        curl_close($ch);

        // Lógica de erro melhorada
        if ($httpcode == 200) {
            http_response_code(200);
            echo json_encode(array("message" => "Agendamento cancelado e cliente notificado."));
        } else {
            $error_message = "Agendamento cancelado, mas falha ao notificar o cliente.";
            if ($curl_error) {
                // Se houver um erro de conexão, a mensagem será mais clara
                $error_message .= " (Erro de conexão: não foi possível encontrar o bot. Verifique se o bot.js está rodando no terminal).";
            } else {
                $error_message .= " Resposta do bot: " . $response;
            }
            http_response_code(200); // Mesmo que a msg falhe, o cancelamento no DB deu certo
            echo json_encode(array("message" => $error_message));
        }

    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Não foi possível cancelar o agendamento no banco de dados."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Dados incompletos para o cancelamento."));
}
?>

<?php
// api/classes/Agendamento.php
class Agendamento {
    private $conn;
    private $table_name = "agendamentos";

    public $id;
    public $nome_cliente;
    public $telefone_cliente;
    public $servico_nome;
    public $servico_duracao;
    public $data_hora;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para ler agendamentos
    public function getAgendamentosPorPeriodo($periodo = 'todos') {
        $query = "SELECT * FROM " . $this->table_name;

        // CORREÇÃO APLICADA AQUI!
        // Esta consulta busca todos os agendamentos cuja DATA seja igual ou maior que a data de HOJE.
        // É mais confiável que usar a hora exata (NOW()).
        $query .= " WHERE DATE(data_hora) >= CURDATE()";
        
        $query .= " ORDER BY data_hora ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Método para cancelar um agendamento
    public function cancelar() {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->status = htmlspecialchars(strip_tags('Cancelado'));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Método para criar um novo agendamento
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    nome_cliente=:nome_cliente,
                    telefone_cliente=:telefone_cliente,
                    servico_nome=:servico_nome,
                    servico_duracao=:servico_duracao,
                    data_hora=:data_hora,
                    status='Confirmado'";

        $stmt = $this->conn->prepare($query);

        $this->nome_cliente = htmlspecialchars(strip_tags($this->nome_cliente));
        $this->telefone_cliente = htmlspecialchars(strip_tags($this->telefone_cliente));
        $this->servico_nome = htmlspecialchars(strip_tags($this->servico_nome));
        $this->servico_duracao = htmlspecialchars(strip_tags($this->servico_duracao));
        $this->data_hora = htmlspecialchars(strip_tags($this->data_hora));

        $stmt->bindParam(":nome_cliente", $this->nome_cliente);
        $stmt->bindParam(":telefone_cliente", $this->telefone_cliente);
        $stmt->bindParam(":servico_nome", $this->servico_nome);
        $stmt->bindParam(":servico_duracao", $this->servico_duracao);
        $stmt->bindParam(":data_hora", $this->data_hora);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

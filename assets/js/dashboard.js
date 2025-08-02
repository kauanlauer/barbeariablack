// assets/js/dashboard.js
$(document).ready(function() {
    // Guarda os IDs dos agendamentos que já estão na tela
    let agendamentosExibidos = new Set();
    const notificationSound = $('#notification-sound')[0];
    const soundActivationModal = new bootstrap.Modal(document.getElementById('soundActivationModal'));

    // Mostra o modal para o usuário interagir e liberar o som
    soundActivationModal.show();

    function carregarAgendamentos() {
        $.ajax({
            url: 'api/get_agendamentos.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const tabela = $('#tabela-agendamentos');
                const semAgendamentosMsg = $('#sem-agendamentos');
                let novidades = false;

                semAgendamentosMsg.toggleClass('d-none', data.length > 0);
                $('#tabela-agendamentos').closest('.table-responsive').toggleClass('d-none', data.length === 0);

                const idsAtuais = new Set(data.map(ag => ag.id));

                data.forEach(function(ag) {
                    if (!agendamentosExibidos.has(ag.id)) {
                        novidades = true;
                        let statusClass = ag.status === 'Confirmado' ? 'status-confirmado' : 'status-cancelado';
                        
                        // Adiciona o telefone do cliente ao botão de cancelar
                        let acoes = ag.status === 'Confirmado' ?
                            `<button class="btn btn-sm btn-outline-danger btn-cancelar" data-id="${ag.id}" data-telefone="${ag.telefone_cliente}"><i class="bi bi-x-circle"></i> Cancelar</button>` :
                            '';

                        let linha = `
                            <tr id="ag-${ag.id}" class="new-row-animation">
                                <td>${ag.data_completa}</td>
                                <td>${ag.hora}</td>
                                <td>${ag.nome_cliente}</td>
                                <td>${ag.telefone_cliente}</td>
                                <td>${ag.servico_nome}</td>
                                <td>${ag.servico_duracao} min</td>
                                <td><span class="${statusClass}">${ag.status}</span></td>
                                <td>${acoes}</td>
                            </tr>
                        `;
                        tabela.prepend(linha);
                    } else {
                        // Atualiza o status de uma linha existente se ele mudou (ex: foi cancelado)
                        const linhaExistente = $(`#ag-${ag.id}`);
                        const statusAtualNaTela = linhaExistente.find('span').text();
                        if(statusAtualNaTela !== ag.status) {
                             let statusClass = ag.status === 'Confirmado' ? 'status-confirmado' : 'status-cancelado';
                             linhaExistente.find('span').text(ag.status).attr('class', statusClass);
                             linhaExistente.find('.btn-cancelar').remove(); // Remove o botão se foi cancelado
                        }
                    }
                });
                
                agendamentosExibidos.forEach(id => {
                    if (!idsAtuais.has(id)) {
                        $(`#ag-${id}`).remove();
                    }
                });

                if (novidades && agendamentosExibidos.size > 0) {
                    notificationSound.play().catch(e => console.error("Erro ao tocar som:", e));
                }
                
                agendamentosExibidos = idsAtuais;
            },
            error: function(xhr, status, error) {
                console.error("Erro ao buscar agendamentos:", error);
            }
        });
    }

    // Evento de clique para o botão de cancelar (ATUALIZADO)
    $(document).on('click', '.btn-cancelar', function() {
        const agendamentoId = $(this).data('id');
        const telefoneCliente = $(this).data('telefone');
        
        // Pede o motivo do cancelamento
        const motivo = prompt("Por favor, digite o motivo do cancelamento (será enviado ao cliente):");

        // Se o barbeiro escreveu um motivo e não clicou em "cancelar" no prompt
        if (motivo !== null && motivo.trim() !== "") {
            $.ajax({
                url: 'api/cancelar_agendamento.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    id: agendamentoId,
                    telefone: telefoneCliente,
                    motivo: motivo 
                }),
                success: function(response) {
                    alert(response.message);
                    carregarAgendamentos(); 
                },
                error: function() {
                    alert('Não foi possível cancelar o agendamento.');
                }
            });
        } else if (motivo !== null) {
            alert("O motivo não pode estar em branco.");
        }
    });

    $('#soundActivationModal').on('hidden.bs.modal', function () {
        carregarAgendamentos();
        setInterval(carregarAgendamentos, 5000); 
    });
});

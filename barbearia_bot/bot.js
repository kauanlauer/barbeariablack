// =========================================================================
// ARQUIVO: /barbearia_bot/bot.js
// Versão final com API Express para receber pedidos de envio de mensagem.
// =========================================================================

const { Client, LocalAuth } = require('whatsapp-web.js');
const { format, parseISO } = require('date-fns');
const { ptBR } = require('date-fns/locale');
const qrcode = require('qrcode');
const { WebSocketServer } = require('ws');
const express = require('express'); // Importa o Express

// --- Configurações ---
const API_URL = 'http://localhost/theblackbeard/api';
const WEBSOCKET_PORT = 8080;
const EXPRESS_PORT = 8081; // Porta para a API do bot

// --- "Memória" do Bot ---
const userState = {};

console.log('Iniciando o bot da The Black Beard Barbershop...');

// --- Configuração do Servidor Express ---
const app = express();
app.use(express.json());

// Endpoint para enviar mensagens
app.post('/send-message', async (req, res) => {
    const { to, text } = req.body;
    if (!to || !text) {
        return res.status(400).json({ success: false, message: 'Parâmetros "to" e "text" são obrigatórios.' });
    }
    try {
        await client.sendMessage(to, text);
        console.log(`Mensagem enviada para ${to}`);
        res.status(200).json({ success: true, message: 'Mensagem enviada.' });
    } catch (error) {
        console.error(`Falha ao enviar mensagem para ${to}:`, error);
        res.status(500).json({ success: false, message: 'Falha ao enviar mensagem pelo WhatsApp.' });
    }
});

app.listen(EXPRESS_PORT, () => {
    console.log(`API do bot para envio de mensagens rodando na porta ${EXPRESS_PORT}`);
});


// --- Configuração do WebSocket Server (sem alterações) ---
const wss = new WebSocketServer({ port: WEBSOCKET_PORT });
let qrCodeDataUrl = '';
wss.on('connection', (ws) => {
    console.log('Painel de QR Code conectado.');
    if (qrCodeDataUrl) {
        ws.send(JSON.stringify({ type: 'qrcode', data: qrCodeDataUrl }));
    }
});
function broadcast(data) {
    wss.clients.forEach(client => {
        if (client.readyState === 1) { client.send(JSON.stringify(data)); }
    });
}
console.log(`Servidor de WebSocket para QR Code rodando na porta ${WEBSOCKET_PORT}`);


// --- Configuração do Cliente WhatsApp ---
const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: { args: ['--no-sandbox', '--disable-setuid-sandbox'] }
});

client.on('qr', async (qr) => {
    console.log('QR Code gerado. Enviando para a página web...');
    qrCodeDataUrl = await qrcode.toDataURL(qr);
    broadcast({ type: 'qrcode', data: qrCodeDataUrl });
});

client.on('ready', () => {
    console.log('Bot conectado e pronto para uso!');
    qrCodeDataUrl = '';
    broadcast({ type: 'status', message: 'Bot conectado com sucesso!' });
});

client.on('disconnected', () => {
    console.log('Bot desconectado.');
    broadcast({ type: 'status', message: 'Bot desconectado. A página será atualizada.' });
});


// --- Lógica de Conversa (sem alterações) ---
client.on('message', async (message) => {
    const from = message.from;
    const text = message.body.trim();
    const state = userState[from] || {};
    try {
        if (state.step === 'awaiting_timeslot' && !isNaN(text)) { await handleTimeSlotChoice(message, text, state); return; }
        if (state.step === 'awaiting_service' && !isNaN(text)) { await handleServiceChoice(message, text); return; }
        if (['oi', 'olá', 'ola', 'agendar'].includes(text.toLowerCase())) { await startNewConversation(message); }
    } catch (error) {
        console.error(`Erro ao processar mensagem de ${from}:`, error);
        await client.sendMessage(from, '😕 Ops! Ocorreu um erro inesperado. Tente começar de novo enviando "oi".');
        delete userState[from];
    }
});

async function startNewConversation(message) { /* ...código sem alteração... */ }
async function handleServiceChoice(message, text) { /* ...código sem alteração... */ }
async function handleTimeSlotChoice(message, text, state) { /* ...código sem alteração... */ }
// Cole as funções startNewConversation, handleServiceChoice e handleTimeSlotChoice da versão anterior aqui.
// Elas não mudaram.

client.initialize();

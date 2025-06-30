<?php

namespace Hogwarts\Service;

class AlertaService
{
    private string $caminhoDB;

    public function __construct()
    {
        $this->caminhoDB = __DIR__ . '/../../../data/database.json';
        $this->garantirBanco();
    }

    private function garantirBanco(): void
    {
        $pasta = dirname($this->caminhoDB);

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        if (!file_exists($this->caminhoDB)) {
            $conteudoInicial = json_encode([
                "alunos" => [],
                "professores" => [],
                "casas" => [],
                "torneios" => [],
                "disciplinas" => [],
                "alertas" => []
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            file_put_contents($this->caminhoDB, $conteudoInicial);
        }
    }

    public function enviarAlerta(): void
    {
        echo "\n✉️ Enviar novo alerta\n";

        echo "Título do alerta: ";
        $titulo = trim(fgets(STDIN));

        echo "Mensagem do alerta: ";
        $mensagem = trim(fgets(STDIN));

        echo "Remetente (ex: Professor Snape): ";
        $remetente = trim(fgets(STDIN));

        echo "Destinatários (separe casas por vírgula, ou escreva 'Todos'): ";
        $destinatariosRaw = trim(fgets(STDIN));
        $destinatarios = array_map('trim', explode(',', $destinatariosRaw));

        echo "Agendar para (YYYY-MM-DD HH:MM:SS) ou ENTER para envio imediato: ";
        $agendadoParaRaw = trim(fgets(STDIN));
        $agendadoPara = $agendadoParaRaw === '' ? null : $agendadoParaRaw;

        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        $alerta = [
            'id' => uniqid(),
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'remetente' => $remetente,
            'destinatarios' => $destinatarios,
            'data_envio' => date('Y-m-d H:i:s'),
            'agendado_para' => $agendadoPara,
            'lido_por' => []
        ];

        $dados['alertas'][] = $alerta;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "✅ Alerta enviado/agendado com sucesso!\n";
    }

    public function listarAlertasParaUsuario(string $usuarioId): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);
        $agora = new \DateTime();

        if (empty($dados['alertas'])) {
            echo "\n❌ Nenhum alerta disponível.\n";
            return;
        }

        $alertasVisiveis = [];
        foreach ($dados['alertas'] as $alerta) {
            $dataAgendada = $alerta['agendado_para'] ? new \DateTime($alerta['agendado_para']) : null;

            if (
                ($alerta['agendado_para'] === null || $dataAgendada <= $agora) &&
                (
                    in_array("Todos", $alerta['destinatarios']) 
                    || in_array($usuarioId, $alerta['destinatarios'])
                    || $this->usuarioPertenceCasa($usuarioId, $alerta['destinatarios'], $dados)
                )
            ) {
                $alertasVisiveis[] = $alerta;
            }
        }

        if (empty($alertasVisiveis)) {
            echo "\n❌ Nenhum alerta ativo para você.\n";
            return;
        }

        echo "\n🔔 Alertas ativos para você:\n";
        foreach ($alertasVisiveis as $alerta) {
            $lido = in_array($usuarioId, $alerta['lido_por']) ? " (Lido)" : " (Não lido)";
            echo "- [{$alerta['data_envio']}] {$alerta['titulo']}{$lido}\n";
            echo "  De: {$alerta['remetente']}\n";
            echo "  Mensagem: {$alerta['mensagem']}\n\n";
        }
    }

    public function marcarComoLido(string $usuarioId): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['alertas'])) {
            echo "\n❌ Nenhum alerta disponível.\n";
            return;
        }

        $alertasVisiveis = [];
        foreach ($dados['alertas'] as $key => $alerta) {
            if (
                in_array("Todos", $alerta['destinatarios']) 
                || in_array($usuarioId, $alerta['destinatarios']) 
                || $this->usuarioPertenceCasa($usuarioId, $alerta['destinatarios'], $dados)
            ) {
                $alertasVisiveis[$key] = $alerta;
            }
        }

        if (empty($alertasVisiveis)) {
            echo "\n❌ Nenhum alerta para você.\n";
            return;
        }

        echo "\n📋 Alertas disponíveis para marcar como lido:\n";
        foreach ($alertasVisiveis as $index => $alerta) {
            $lido = in_array($usuarioId, $alerta['lido_por']) ? " (Lido)" : "";
            echo ($index + 1) . ". {$alerta['titulo']}{$lido}\n";
        }

        echo "Escolha o alerta para marcar como lido: ";
        $opcao = intval(trim(fgets(STDIN))) - 1;

        if (!isset($alertasVisiveis[$opcao])) {
            echo "Opção inválida.\n";
            return;
        }

        $key = array_keys($alertasVisiveis)[$opcao];

        if (!in_array($usuarioId, $dados['alertas'][$key]['lido_por'])) {
            $dados['alertas'][$key]['lido_por'][] = $usuarioId;
            file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo "✅ Alerta marcado como lido.\n";
        } else {
            echo "⚠️ Alerta já estava marcado como lido.\n";
        }
    }

    private function usuarioPertenceCasa(string $usuarioId, array $destinatarios, array $dados): bool
    {
        
        foreach ($dados['alunos'] as $aluno) {
            if ($aluno['id'] === $usuarioId && isset($aluno['casa']) && in_array($aluno['casa'], $destinatarios)) {
                return true;
            }
        }
        
        foreach ($dados['professores'] as $professor) {
            if ($professor['id'] === $usuarioId && isset($professor['casa']) && in_array($professor['casa'], $destinatarios)) {
                return true;
            }
        }
        return false;
    }
}

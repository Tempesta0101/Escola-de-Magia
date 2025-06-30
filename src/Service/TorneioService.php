<?php

namespace Hogwarts\Service;

class TorneioService
{
    private string $caminhoDB;

    public function __construct()
    {
        $this->caminhoDB = __DIR__ . '/../../../data/database.json';
    }

    public function criarTorneio(): void
    {
        echo "\n🏆 Criar Novo Torneio\n";

        echo "Nome do torneio: ";
        $nome = trim(fgets(STDIN));

        echo "Data do torneio (YYYY-MM-DD): ";
        $data = trim(fgets(STDIN));

        echo "Local do torneio: ";
        $local = trim(fgets(STDIN));

        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        $torneio = [
            'id' => uniqid(),
            'nome' => $nome,
            'data' => $data,
            'local' => $local,
            'inscritos' => [],  // lista de alunos inscritos (ids)
            'desempenhos' => [], // registros dos desempenhos
            'pontuacao_casas' => [  // pontuação inicial zerada
                'Grifinória' => 0,
                'Sonserina' => 0,
                'Corvinal' => 0,
                'Lufa-Lufa' => 0,
            ],
        ];

        $dados['torneios'][] = $torneio;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "✅ Torneio '{$nome}' criado com sucesso!\n";
    }

    public function inscreverAluno(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['torneios'])) {
            echo "\n❌ Nenhum torneio cadastrado.\n";
            return;
        }

        if (empty($dados['alunos'])) {
            echo "\n❌ Nenhum aluno cadastrado.\n";
            return;
        }

        echo "\n🏆 Inscrição de Aluno em Torneio\n";

        echo "Torneios disponíveis:\n";
        foreach ($dados['torneios'] as $index => $torneio) {
            echo ($index + 1) . ". " . $torneio['nome'] . " (Data: " . $torneio['data'] . ")\n";
        }

        echo "Escolha o número do torneio: ";
        $torneioEscolhido = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['torneios'][$torneioEscolhido])) {
            echo "Opção inválida.\n";
            return;
        }

        $torneio = &$dados['torneios'][$torneioEscolhido];

        $alunosNaoInscritos = array_filter($dados['alunos'], function ($aluno) use ($torneio) {
            return !in_array($aluno['id'], $torneio['inscritos']);
        });

        if (empty($alunosNaoInscritos)) {
            echo "Todos os alunos já estão inscritos nesse torneio.\n";
            return;
        }

        echo "Alunos disponíveis para inscrição:\n";
        $alunosChaves = array_keys($alunosNaoInscritos);
        foreach ($alunosNaoInscritos as $idx => $aluno) {
            echo ($idx + 1) . ". " . $aluno['nome'] . " (Casa: " . ($aluno['casa'] ?? 'Sem casa') . ")\n";
        }

        echo "Escolha o número do aluno para inscrever: ";
        $alunoEscolhido = intval(trim(fgets(STDIN))) - 1;

        if (!isset($alunosNaoInscritos[$alunoEscolhido])) {
            echo "Opção inválida.\n";
            return;
        }

        $alunoKeys = array_keys($alunosNaoInscritos);
        $alunoId = $alunosNaoInscritos[$alunoKeys[$alunoEscolhido]]['id'];

        $torneio['inscritos'][] = $alunoId;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "✅ Aluno inscrito com sucesso no torneio '{$torneio['nome']}'!\n";
    }

    public function registrarDesempenho(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['torneios'])) {
            echo "\n❌ Nenhum torneio cadastrado.\n";
            return;
        }

        echo "\n🏆 Registrar Desempenho\n";

        foreach ($dados['torneios'] as $index => $torneio) {
            echo ($index + 1) . ". " . $torneio['nome'] . "\n";
        }
        echo "Escolha o número do torneio: ";
        $torneioEscolhido = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['torneios'][$torneioEscolhido])) {
            echo "Opção inválida.\n";
            return;
        }

        $torneio = &$dados['torneios'][$torneioEscolhido];

        if (empty($torneio['inscritos'])) {
            echo "Nenhum aluno inscrito nesse torneio.\n";
            return;
        }

        echo "Alunos inscritos:\n";
        $alunosInscritos = [];
        foreach ($torneio['inscritos'] as $idx => $alunoId) {
            $aluno = $this->buscarAlunoPorId($dados['alunos'], $alunoId);
            if ($aluno) {
                $alunosInscritos[] = $aluno;
                echo ($idx + 1) . ". " . $aluno['nome'] . " (Casa: " . ($aluno['casa'] ?? 'Sem casa') . ")\n";
            }
        }

        echo "Escolha o número do aluno para registrar desempenho: ";
        $alunoEscolhido = intval(trim(fgets(STDIN))) - 1;

        if (!isset($alunosInscritos[$alunoEscolhido])) {
            echo "Opção inválida.\n";
            return;
        }

        $aluno = $alunosInscritos[$alunoEscolhido];

        echo "Digite a pontuação obtida pelo aluno: ";
        $pontuacao = intval(trim(fgets(STDIN)));

        $torneio['desempenhos'][] = [
            'aluno_id' => $aluno['id'],
            'pontuacao' => $pontuacao,
        ];

        $casa = $aluno['casa'] ?? null;
        if ($casa && isset($torneio['pontuacao_casas'][$casa])) {
            $torneio['pontuacao_casas'][$casa] += $pontuacao;
        }

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "✅ Desempenho registrado para o aluno {$aluno['nome']}!\n";
    }

    public function mostrarRanking(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['torneios'])) {
            echo "\n❌ Nenhum torneio cadastrado.\n";
            return;
        }

        echo "\n🏆 Ranking dos torneios\n";

        foreach ($dados['torneios'] as $torneio) {
            echo "\nTorneio: {$torneio['nome']} (Data: {$torneio['data']})\n";
            echo "Pontuação das casas:\n";

            arsort($torneio['pontuacao_casas']);

            foreach ($torneio['pontuacao_casas'] as $casa => $pontos) {
                echo "  {$casa}: {$pontos}\n";
            }
        }
    }

    private function buscarAlunoPorId(array $alunos, string $id): ?array
    {
        foreach ($alunos as $aluno) {
            if ($aluno['id'] === $id) {
                return $aluno;
            }
        }
        return null;
    }
}

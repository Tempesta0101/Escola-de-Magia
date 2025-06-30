<?php

namespace Hogwarts\Service;

class DisciplinaService
{
    private string $caminhoDB;

    public function __construct()
    {
        $this->caminhoDB = __DIR__ . '/../../../data/database.json';
    }

    // Registrar nota de um aluno em uma disciplina
    public function registrarNota(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['disciplinas'])) {
            echo "\n❌ Nenhuma disciplina cadastrada.\n";
            return;
        }
        if (empty($dados['alunos'])) {
            echo "\n❌ Nenhum aluno cadastrado.\n";
            return;
        }

        echo "\n📚 Registrar Nota\n";

        // Listar disciplinas
        foreach ($dados['disciplinas'] as $index => $disciplina) {
            echo ($index + 1) . ". " . $disciplina['nome'] . "\n";
        }
        echo "Escolha a disciplina: ";
        $disciplinaIndex = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['disciplinas'][$disciplinaIndex])) {
            echo "Opção inválida.\n";
            return;
        }
        $disciplina = &$dados['disciplinas'][$disciplinaIndex];

        // Listar alunos
        foreach ($dados['alunos'] as $index => $aluno) {
            echo ($index + 1) . ". " . $aluno['nome'] . " (Casa: " . ($aluno['casa'] ?? "Sem casa") . ")\n";
        }
        echo "Escolha o aluno: ";
        $alunoIndex = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['alunos'][$alunoIndex])) {
            echo "Opção inválida.\n";
            return;
        }
        $aluno = $dados['alunos'][$alunoIndex];

        echo "Informe a nota (0-100): ";
        $nota = floatval(trim(fgets(STDIN)));

        if ($nota < 0 || $nota > 100) {
            echo "Nota inválida.\n";
            return;
        }

        // Registra a nota
        $registro = [
            'aluno_id' => $aluno['id'],
            'nota' => $nota,
            'data' => date('Y-m-d H:i:s'),
        ];

        // Inicializa se não existir
        if (!isset($disciplina['notas'])) {
            $disciplina['notas'] = [];
        }

        $disciplina['notas'][] = $registro;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "✅ Nota registrada com sucesso para {$aluno['nome']} na disciplina {$disciplina['nome']}.\n";
    }

    // Consultar boletim de um aluno
    public function consultarBoletim(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['alunos'])) {
            echo "\n❌ Nenhum aluno cadastrado.\n";
            return;
        }
        if (empty($dados['disciplinas'])) {
            echo "\n❌ Nenhuma disciplina cadastrada.\n";
            return;
        }

        echo "\n📄 Consultar Boletim\n";

        foreach ($dados['alunos'] as $index => $aluno) {
            echo ($index + 1) . ". " . $aluno['nome'] . "\n";
        }
        echo "Escolha o aluno para consultar o boletim: ";
        $alunoIndex = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['alunos'][$alunoIndex])) {
            echo "Opção inválida.\n";
            return;
        }
        $aluno = $dados['alunos'][$alunoIndex];

        echo "\nBoletim de {$aluno['nome']}:\n";

        $notasEncontradas = false;

        foreach ($dados['disciplinas'] as $disciplina) {
            $notas = $disciplina['notas'] ?? [];
            $notasAluno = array_filter($notas, fn($n) => $n['aluno_id'] === $aluno['id']);
            if ($notasAluno) {
                $notasEncontradas = true;
                echo "- " . $disciplina['nome'] . ": ";
                foreach ($notasAluno as $nota) {
                    echo number_format($nota['nota'], 1) . " (registrada em {$nota['data']}) ";
                }
                echo "\n";
            }
        }

        if (!$notasEncontradas) {
            echo "Nenhuma nota registrada para este aluno.\n";
        }
    }

    // Aplicar penalidade ou bônus de pontos para a casa
    public function aplicarPenalidadeBonus(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        echo "\n⚖️ Aplicar Penalidade ou Bônus de Pontos para Casa\n";

        $casas = ['Grifinória', 'Sonserina', 'Corvinal', 'Lufa-Lufa'];

        // Mostrar casas
        foreach ($casas as $index => $casa) {
            echo ($index + 1) . ". " . $casa . "\n";
        }

        echo "Escolha a casa: ";
        $casaIndex = intval(trim(fgets(STDIN))) - 1;

        if (!isset($casas[$casaIndex])) {
            echo "Opção inválida.\n";
            return;
        }
        $casa = $casas[$casaIndex];

        echo "Informe pontos (positivos para bônus, negativos para penalidade): ";
        $pontos = intval(trim(fgets(STDIN)));

        // Inicializa pontuação das casas se não existir
        if (!isset($dados['pontuacao_casas'])) {
            $dados['pontuacao_casas'] = [
                'Grifinória' => 0,
                'Sonserina' => 0,
                'Corvinal' => 0,
                'Lufa-Lufa' => 0,
            ];
        }

        if (!isset($dados['pontuacao_casas'][$casa])) {
            $dados['pontuacao_casas'][$casa] = 0;
        }

        $dados['pontuacao_casas'][$casa] += $pontos;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $tipo = $pontos >= 0 ? 'Bônus' : 'Penalidade';
        echo "✅ {$tipo} de {$pontos} pontos aplicado(s) para a casa {$casa}.\n";
    }
    public function cadastrarDisciplina(): void
{
    $dados = json_decode(file_get_contents($this->caminhoDB), true);

    echo "\n➕ Cadastro de Nova Disciplina\n";
    echo "Nome da disciplina: ";
    $nome = trim(fgets(STDIN));

    $novaDisciplina = [
        'id' => uniqid(),
        'nome' => $nome,
        'notas' => []
    ];

    $dados['disciplinas'][] = $novaDisciplina;

    file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "✅ Disciplina '{$nome}' cadastrada com sucesso!\n";
}

}


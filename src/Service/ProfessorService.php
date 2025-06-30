<?php

namespace Hogwarts\Service;

class ProfessorService
{
    private string $caminhoDB;

    public function __construct()
    {
        $this->caminhoDB = __DIR__ . '/../../../data/database.json';
    }

    public function cadastrarProfessor(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        echo "\n👨‍🏫 Cadastro de Professor\n";
        echo "Nome completo: ";
        $nome = trim(fgets(STDIN));

        echo "Disciplina(s) que leciona (separadas por vírgula): ";
        $disciplinas = array_map('trim', explode(',', fgets(STDIN)));

        echo "Horário(s) disponíveis (ex: Segunda 10h, Terça 14h): ";
        $horarios = array_map('trim', explode(',', fgets(STDIN)));

        echo "Turmas (opcional, separadas por vírgula): ";
        $turmas = array_map('trim', explode(',', fgets(STDIN)));

        $professor = [
            'id' => uniqid(),
            'nome' => $nome,
            'disciplinas' => $disciplinas,
            'horarios' => $horarios,
            'turmas' => $turmas,
        ];

        $dados['professores'][] = $professor;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "✅ Professor {$nome} cadastrado com sucesso!\n";
    }

    public function consultarCronograma(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['professores'])) {
            echo "\n❌ Nenhum professor cadastrado ainda.\n";
            return;
        }

        echo "\n📅 Cronograma de Professores\n";
        foreach ($dados['professores'] as $index => $prof) {
            echo ($index + 1) . ". {$prof['nome']}\n";
        }

        echo "Escolha o número do professor: ";
        $escolha = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['professores'][$escolha])) {
            echo "Opção inválida.\n";
            return;
        }

        $prof = $dados['professores'][$escolha];

        echo "\n🧾 Cronograma de {$prof['nome']}:\n";
        echo "- Disciplinas: " . implode(', ', $prof['disciplinas']) . "\n";
        echo "- Horários: " . implode(', ', $prof['horarios']) . "\n";
        echo "- Turmas: " . implode(', ', $prof['turmas']) . "\n";
    }
}

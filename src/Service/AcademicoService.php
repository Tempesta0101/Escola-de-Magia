<?php

namespace Hogwarts\Service;

class DisciplinaService
{
    private string $caminhoDB;

    public function __construct()
    {
        $this->caminhoDB = __DIR__ . '/../../../data/database.json';
    }

    public function registrarNota(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        echo "\n\ud83d\udcd3 Registro de Nota\n";
        echo "Nome do aluno: ";
        $nome = trim(fgets(STDIN));

        $aluno = null;
        foreach ($dados['alunos'] as $a) {
            if (strtolower($a['nome']) === strtolower($nome)) {
                $aluno = $a;
                break;
            }
        }

        if (!$aluno) {
            echo "Aluno n\u00e3o encontrado.\n";
            return;
        }

        echo "Disciplina: ";
        $disciplina = trim(fgets(STDIN));

        echo "Nota (0 a 100): ";
        $nota = floatval(trim(fgets(STDIN)));

        $dados['notas'][] = [
            'aluno_id' => $aluno['id'],
            'nome' => $aluno['nome'],
            'disciplina' => ucfirst($disciplina),
            'nota' => $nota,
            'data' => date('Y-m-d')
        ];

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n✅ Nota registrada com sucesso.\n";
    }

    public function consultarBoletim(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        echo "\n\ud83d\udcc4 Consultar Boletim\n";
        echo "Nome do aluno: ";
        $nome = trim(fgets(STDIN));

        $notasAluno = array_filter($dados['notas'], fn($n) => strtolower($n['nome']) === strtolower($nome));

        if (empty($notasAluno)) {
            echo "Nenhuma nota encontrada para esse aluno.\n";
            return;
        }

        echo "\nBoletim de {$nome}:\n";
        foreach ($notasAluno as $nota) {
            echo "- {$nota['disciplina']}: {$nota['nota']}\n";
        }
    }

    public function aplicarPenalidadeBonus(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        echo "\n\ud83c\udf1f Aplicar B\u00f4nus ou Penalidade\n";
        echo "Nome da casa: ";
        $casa = strtolower(trim(fgets(STDIN)));

        echo "Quantidade de pontos (+ ou -): ";
        $pontos = intval(trim(fgets(STDIN)));

        $dados['ranking_casas'][$casa] = ($dados['ranking_casas'][$casa] ?? 0) + $pontos;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n\u2705 Pontua\u00e7\u00e3o atualizada com sucesso.\n";
    }
}

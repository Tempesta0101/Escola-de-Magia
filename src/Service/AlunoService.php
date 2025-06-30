<?php

namespace Hogwarts\Service;

class AlunoService
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
                "disciplinas" => []
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            file_put_contents($this->caminhoDB, $conteudoInicial);
        }
    }

    public function convidarNovoAluno(): void
    {
        echo "\n🔔 CONVITE DE NOVO ALUNO\n";

        echo "Nome completo: ";
        $nome = trim(fgets(STDIN));

        echo "Idade: ";
        $idade = intval(trim(fgets(STDIN)));

        echo "Tipo sanguíneo mágico (Puro-sangue, Mestiço, Trouxa): ";
        $sangue = trim(fgets(STDIN));

        echo "Família de origem (sobrenome ou clã): ";
        $familia = trim(fgets(STDIN));

        $aluno = [
            'id' => uniqid(),
            'nome' => $nome,
            'idade' => $idade,
            'sangue' => ucfirst(strtolower($sangue)),
            'familia' => $familia,
            'data_convite' => date('Y-m-d H:i:s')
        ];

        $dados = json_decode(file_get_contents($this->caminhoDB), true);
        $dados['alunos'][] = $aluno;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n✅ Aluno convidado com sucesso!\n";
    }

    // Listar alunos
    public function listarAlunos(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);
        if (empty($dados['alunos'])) {
            echo "\n⚠️ Nenhum aluno cadastrado.\n";
            return;
        }

        echo "\n📋 Lista de Alunos:\n";
        foreach ($dados['alunos'] as $index => $aluno) {
            echo ($index + 1) . ". {$aluno['nome']} (Idade: {$aluno['idade']}, Sangue: {$aluno['sangue']}, Família: {$aluno['familia']})\n";
        }
    }

    // Editar aluno
    public function editarAluno(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['alunos'])) {
            echo "\n⚠️ Nenhum aluno cadastrado.\n";
            return;
        }

        $this->listarAlunos();

        echo "Escolha o número do aluno que deseja editar: ";
        $indice = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['alunos'][$indice])) {
            echo "Opção inválida.\n";
            return;
        }

        $aluno = $dados['alunos'][$indice];

        echo "Novo nome ({$aluno['nome']}): ";
        $novoNome = trim(fgets(STDIN));
        if ($novoNome !== '') {
            $aluno['nome'] = $novoNome;
        }

        echo "Nova idade ({$aluno['idade']}): ";
        $novaIdade = trim(fgets(STDIN));
        if ($novaIdade !== '') {
            $aluno['idade'] = intval($novaIdade);
        }

        echo "Novo tipo sanguíneo mágico ({$aluno['sangue']}): ";
        $novoSangue = trim(fgets(STDIN));
        if ($novoSangue !== '') {
            $aluno['sangue'] = ucfirst(strtolower($novoSangue));
        }

        echo "Nova família ({$aluno['familia']}): ";
        $novaFamilia = trim(fgets(STDIN));
        if ($novaFamilia !== '') {
            $aluno['familia'] = $novaFamilia;
        }

        $dados['alunos'][$indice] = $aluno;

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n✅ Aluno atualizado com sucesso!\n";
    }

    // Remover aluno
    public function removerAluno(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        if (empty($dados['alunos'])) {
            echo "\n⚠️ Nenhum aluno cadastrado.\n";
            return;
        }

        $this->listarAlunos();

        echo "Escolha o número do aluno que deseja remover: ";
        $indice = intval(trim(fgets(STDIN))) - 1;

        if (!isset($dados['alunos'][$indice])) {
            echo "Opção inválida.\n";
            return;
        }

        $nome = $dados['alunos'][$indice]['nome'];

        // Remove o aluno
        array_splice($dados['alunos'], $indice, 1);

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n✅ Aluno '{$nome}' removido com sucesso!\n";
    }
}

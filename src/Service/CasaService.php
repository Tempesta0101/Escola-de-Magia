<?php

namespace Hogwarts\Service;

class CasaService
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

    public function selecionarCasa(): void
    {
        $dados = json_decode(file_get_contents($this->caminhoDB), true);

        // Filtra alunos que ainda não têm casa
        $alunosSemCasa = array_filter($dados['alunos'], fn($a) => empty($a['casa']));

        if (empty($alunosSemCasa)) {
            echo "\n🎩 Todos os alunos já foram selecionados para uma casa.\n";
            return;
        }

        echo "\n🎩 Alunos disponíveis para seleção:\n";
        foreach ($alunosSemCasa as $index => $aluno) {
            echo ($index + 1) . ". " . $aluno['nome'] . "\n";
        }

        echo "Escolha o número do aluno para seleção: ";
        $escolha = intval(trim(fgets(STDIN)));

        if (!isset($alunosSemCasa[array_keys($alunosSemCasa)[$escolha -1]])) {
            echo "Opção inválida.\n";
            return;
        }

        $alunoSelecionadoKey = array_keys($alunosSemCasa)[$escolha -1];
        $aluno = $dados['alunos'][$alunoSelecionadoKey];

        echo "\nVamos começar a seleção de casa para: {$aluno['nome']}.\n";

        // Perguntas e palavras-chave para cada casa
        $perguntas = [
            [
                'pergunta' => "1) Qual dessas qualidades você mais valoriza? (Coragem, Ambição, Inteligência, Lealdade)\n",
                'chaves' => [
                    'grifinória' => ['coragem'],
                    'sonserina' => ['ambição'],
                    'corvinal' => ['inteligência'],
                    'lufa-lufa' => ['lealdade'],
                ],
            ],
            [
                'pergunta' => "2) Qual sua maior motivação? (Glória, Conhecimento, Justiça, Poder)\n",
                'chaves' => [
                    'grifinória' => ['glória'],
                    'corvinal' => ['conhecimento'],
                    'lufa-lufa' => ['justiça'],
                    'sonserina' => ['poder'],
                ],
            ],
            [
                'pergunta' => "3) O que você faria diante de um desafio perigoso? (Enfrentar, Planejar, Esperar, Ajudar)\n",
                'chaves' => [
                    'grifinória' => ['enfrentar'],
                    'corvinal' => ['planejar'],
                    'lufa-lufa' => ['esperar', 'ajudar'],
                    'sonserina' => ['planejar'],
                ],
            ],
        ];

        $pontuacoes = [
            'grifinória' => 0,
            'sonserina' => 0,
            'corvinal' => 0,
            'lufa-lufa' => 0,
        ];

        foreach ($perguntas as $p) {
            echo $p['pergunta'];
            $resposta = strtolower(trim(fgets(STDIN)));

            foreach ($p['chaves'] as $casa => $palavras) {
                foreach ($palavras as $palavra) {
                    if (str_contains($resposta, $palavra)) {
                        $pontuacoes[$casa]++;
                    }
                }
            }
        }

        $maiorPontuacao = max($pontuacoes);
        $candidatas = array_keys(array_filter($pontuacoes, fn($p) => $p === $maiorPontuacao));
        $casaEscolhida = $candidatas[array_rand($candidatas)];

        $dados['alunos'][$alunoSelecionadoKey]['casa'] = ucfirst($casaEscolhida);

        file_put_contents($this->caminhoDB, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n✨ O aluno {$aluno['nome']} foi selecionado para a casa " . ucfirst($casaEscolhida) . "!\n";
    }
}

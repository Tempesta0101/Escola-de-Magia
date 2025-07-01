<?php

namespace Hogwarts\Controller;

use Hogwarts\Service\AlunoService;
use Hogwarts\Service\CasaService;
use Hogwarts\Service\TorneioService;
use Hogwarts\Service\DisciplinaService;
use Hogwarts\Service\ProfessorService;
use Hogwarts\Service\AlertaService;


class MenuController
{
    public function mostrarMenuPrincipal(): void
    {
        while (true) {
            echo "\n🔮 Sistema de Gestão Escolar de Hogwarts\n";
            echo "1. Convidar novo aluno\n";
            echo "2. Selecionar casa\n";
            echo "3. Gerenciar alunos\n";
            echo "4. Gerenciar professores\n";
            echo "5. Controle acadêmico\n";
            echo "6. Torneios intercasas\n";
            echo "7. Sistema de Alertas\n";
            echo "0. Sair\n";
            echo "Escolha uma opção: ";

            $opcao = intval(trim(fgets(STDIN)));

            switch ($opcao) {
                case 1:
                    $alunoService = new AlunoService();
                    $alunoService->convidarNovoAluno();
                    break;
                case 2:
                    $casaService = new CasaService();
                    $casaService->selecionarCasa();
                    break;
                case 3:
                    $this->mostrarMenuAlunos();
                    break;
                case 4:
                    $this->mostrarMenuProfessores();
                    break;
                case 5:
                    $this->mostrarMenuControleAcademico();
                    break;
                case 6:
                        $this->mostrarMenuTorneios();
                    break;
                case 7:
                        $this->mostrarMenuAlertas('usuario_teste');
                    break;


                case 0:
                    echo "Até logo!\n";
                    exit;

                default:
                    echo "Opção inválida. Tente novamente.\n";
            }
        }
    }

    private function mostrarMenuTorneios(): void
    {
        $torneioService = new TorneioService();

while (true) {
    echo "\n🎯 Menu de Torneios\n";
    echo "1. Criar novo torneio\n";
    echo "2. Inscrever aluno em torneio\n";
    echo "3. Registrar desempenho\n";
    echo "4. Mostrar ranking das casas\n";
    echo "5. Excluir torneio\n";
    echo "0. Voltar ao menu principal\n";
    echo "Escolha uma opção: ";

    $opcao = trim(fgets(STDIN));

    switch ($opcao) {
        case '1':
            $torneioService->criarTorneio();
            break;
        case '2':
            $torneioService->inscreverAluno();
            break;
        case '3':
            $torneioService->registrarDesempenho();
            break;
        case '4':
            $torneioService->mostrarRanking();
            break;
        case '5':
            $torneioService->excluirTorneio();
            break;
        case '0':
            echo "Voltando ao menu principal...\n";
            exit; 
        default:
            echo "Opção inválida. Tente novamente.\n";
            break;
            }
            
        }
    }
    
public function mostrarMenuControleAcademico(): void
{
    $disciplinaService = new \Hogwarts\Service\DisciplinaService();

    while (true) {
        echo "\n📚 Controle Acadêmico e Disciplinar\n";
        echo "1. Registrar nota\n";
        echo "2. Consultar boletim\n";
        echo "3. Aplicar penalidade/bônus para casas\n";
        echo "0. Voltar\n";
        echo "Escolha uma opção: ";

        $opcao = trim(fgets(STDIN));

        switch ($opcao) {
            case '1':
                $disciplinaService->registrarNota();
                break;
            case '2':
                $disciplinaService->consultarBoletim();
                break;
            case '3':
                $disciplinaService->aplicarPenalidadeBonus();
                break;
            case '0':
                return;
            default:
                echo "Opção inválida.\n";
        }
    }
}
private function mostrarMenuProfessores(): void
{
    $professorService = new ProfessorService();

    while (true) {
        echo "\n👨‍🏫 Gerenciamento de Professores e Funcionários\n";
        echo "1. Cadastrar professor\n";
        echo "2. Consultar cronograma de professor\n";
        echo "0. Voltar\n";
        echo "Escolha uma opção: ";

        $opcao = trim(fgets(STDIN));

        switch ($opcao) {
            case '1':
                $professorService->cadastrarProfessor();
                break;
            case '2':
                $professorService->consultarCronograma();
                break;
            case '0':
                return; 
            default:
                echo "Opção inválida.\n";
        }
    }
}
private function mostrarMenuAlunos(): void
{
    $alunoService = new \Hogwarts\Service\AlunoService();

    while (true) {
        echo "\n👦 Gerenciamento de Alunos\n";
        echo "1. Listar alunos\n";
        echo "2. Editar aluno\n";
        echo "3. Remover aluno\n";
        echo "0. Voltar\n";
        echo "Escolha uma opção: ";

        $opcao = trim(fgets(STDIN));

        switch ($opcao) {
            case '1':
                $alunoService->listarAlunos();
                break;
            case '2':
                $alunoService->editarAluno();
                break;
            case '3':
                $alunoService->removerAluno();
                break;
            case '0':
                return;
            default:
                echo "Opção inválida.\n";
        }
    }
}
private function mostrarMenuAlertas(string $usuarioId): void
{
    $alertaService = new AlertaService();

    while (true) {
        echo "\n📢 Sistema de Alertas e Comunicação\n";
        echo "1. Enviar novo alerta\n";
        echo "2. Listar meus alertas\n";
        echo "3. Marcar alerta como lido\n";
        echo "0. Voltar\n";
        echo "Escolha uma opção: ";

        $opcao = trim(fgets(STDIN));

        switch ($opcao) {
            case '1':
                $alertaService->enviarAlerta();
                break;
            case '2':
                $alertaService->listarAlertasParaUsuario($usuarioId);
                break;
            case '3':
                $alertaService->marcarComoLido($usuarioId);
                break;
            case '0':
                return;
            default:
                echo "Opção inválida.\n";
        }
    }
}




}


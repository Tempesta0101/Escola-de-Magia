Sistema Integrado de Gestão Escolar de Hogwarts
Bem-vindo ao Sistema Integrado de Gestão Escolar de Hogwarts!
Este sistema foi desenvolvido para automatizar e facilitar o gerenciamento escolar do mundo mágico, incluindo alunos, professores, casas, torneios, controle acadêmico e sistema de alertas.

Funcionalidades
1. Convite e Gerenciamento de Alunos
Cadastro completo com dados pessoais e origem mágica.

Seleção de casa.

Visualização e gerenciamento dos alunos cadastrados.

2. Gerenciamento de Casas
Seleção e associação dos alunos às quatro casas: Grifinória, Sonserina, Corvinal e Lufa-Lufa.

3. Gerenciamento de Professores e Funcionários
Cadastro de professores.

Associação a disciplinas e turmas.

Consulta de cronograma de aulas.

4. Controle Acadêmico e Disciplinar
Cadastro e gerenciamento de disciplinas.

Registro e consulta de notas dos alunos.

Aplicação de penalidades e bônus de pontos para as casas.

5. Torneios Intercasas
Criação e gerenciamento de torneios mágicos.

Inscrição de alunos.

Registro de desempenho e pontuação.

Exibição de rankings em tempo real.

6. Sistema de Alertas e Comunicação
Envio de comunicados imediatos ou agendados.

Notificações para alunos, professores e administração.

Marcação de alertas como lidos.

Tecnologias Utilizadas
PHP 8+ com Programação Orientada a Objetos (POO)

Organização de código seguindo padrão PSR-4

Armazenamento dos dados em arquivo JSON para fácil persistência

Interface de linha de comando (CLI) para interação rápida e leve

Estrutura do Projeto
bash
Copiar
Editar
php-poo-skeleton/
├── src/
│   ├── Controller/         # Controladores que gerenciam menus e fluxo
│   ├── Service/            # Serviços com lógica de negócio (alunos, professores, torneios, etc)
├── data/
│   └── database.json       # Banco de dados JSON
├── app.php                 # Arquivo principal para iniciar o sistema
├── composer.json           # Autoload PSR-4 e dependências
└── README.md               # Documentação do projeto
Como Executar
Clonar o repositório
[git clone ](https://github.com/Tempesta0101/Escola-de-Magia.git)

Entrar na pasta do projeto
cd hogwarts-gestao-escolar

Executar o sistema via CLI
php app.php

Navegue pelos menus e gerencie Hogwarts com facilidade!

Próximos Passos:
Implementar interface web para maior usabilidade

Adicionar autenticação e controle de acesso

Melhorar sistema de notificações com envio por email ou push
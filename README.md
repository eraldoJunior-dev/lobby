<div align="center">

LOBBY
Sua comunidade, seus jogos, suas histórias.
Uma rede social para quem vive o universo dos games.

Protótipo visual · Repositório · Issues



</div>

Sobre o projeto
O Lobby é uma plataforma social voltada à comunidade gamer. A proposta é reunir, em um só lugar, publicações, descoberta de jogos, avaliações, listas pessoais, guias, fóruns e notícias.

O projeto começou como um protótipo visual estático e está evoluindo para uma aplicação web em Laravel, com autenticação e persistência de dados.
Status atual: em desenvolvimento. Algumas integrações, capas e funcionalidades ainda precisam de testes e ajustes antes de uma versão beta pública. O chat está fora do escopo atual.

Funcionalidades
Área	O que oferece
Início	Feed de publicações, reações, salvamentos e recomendações de jogos
Jogos	Catálogo, busca, filtros e páginas individuais
Avaliações	Notas e reviews da comunidade, com registro de horas jogadas
Guias	Consulta e publicação de guias, com etapas e progresso
Listas	Organização da biblioteca pessoal e status dos jogos
Fóruns	Tópicos, respostas e discussões por jogo
Notícias	Notícias categorizadas e links “Leia na fonte”
Perfis	Perfil gamer, informações públicas e usuários para seguir
Notificações	Acompanhamento de interações disponíveis
Configurações	Dados da conta, privacidade e preferências


Observação: preços e conquistas podem depender de fontes externas e ainda não estão totalmente integrados ao catálogo. Algumas capas também utilizam imagens externas.
Tecnologias
- PHP 8.3+
- Laravel 13
- Blade, HTML, CSS e JavaScript
- Vite 8 e Tailwind CSS 4 (dependências de frontend)
- SQLite como configuração padrão de desenvolvimento
- Composer, npm e PHPUnit
Como executar localmente
Pré-requisitos
- PHP 8.3 ou superior e extensões necessárias ao Laravel (incluindo SQLite, se usar o banco padrão)
- Composer
- Node.js e npm compatíveis com a versão do Vite
- Git
1. Clone o repositório
git clone https://github.com/eraldoJunior-dev/lobby.git
cd lobby
Se o aplicativo Laravel estiver dentro de uma subpasta lobby/, entre nela antes dos próximos comandos. Use a pasta que contém o arquivo artisan.

2. Instale as dependências
composer install
npm install
3. Configure o ambiente
No Windows (PowerShell):
Copy-Item .env.example .env
No Linux/macOS:
cp .env.example .env
Depois, execute:
php artisan key:generate
O arquivo .env.example já utiliza SQLite por padrão. Confira a configuração de banco no .env e, se necessário, crie o arquivo database/database.sqlite.
4. Prepare o banco de dados
php artisan migrate
php artisan db:seed --class=GameSeeder
O seeder adiciona os jogos de demonstração ao catálogo. Evite executar migrate:fresh em um banco com dados que deseja preservar.
5. Inicie a aplicação
Em um terminal:
npm run dev
Em outro:
php artisan serve
Abra http://localhost:8000 no navegador.

Para gerar os assets de produção, use npm run build.
Testes
php artisan test
O projeto possui testes automatizados, mas a aprovação integral da suíte e os testes de interface/navegador precisam ser confirmados no ambiente de execução. Não considere o aplicativo pronto para produção apenas porque a instalação local funcionou.
Estrutura principal
app/                  # Controllers, models e regras da aplicação
bootstrap/            # Inicialização do Laravel
config/               # Configurações
database/             # Migrations, factories e seeders
public/               # Assets públicos
resources/views/       # Telas Blade
routes/web.php         # Rotas da aplicação
tests/                 # Testes automatizados
Próximos passos
- [ ] Verificar e estabilizar o carregamento das capas dos jogos
- [ ] Completar dados de preços, plataformas e conquistas com fontes confiáveis
- [ ] Revisar as páginas individuais e as interações do catálogo
- [ ] Validar os fluxos de cadastro, login, avaliações e listas no navegador
- [ ] Melhorar responsividade e acessibilidade
- [ ] Revisar segurança, configurações e infraestrutura antes do beta público
- [ ] Documentar API/integrações externas, quando existirem
O chat não faz parte da versão atual e poderá ser reavaliado futuramente.
Contribuindo
Encontrou um bug ou tem uma sugestão? Abra uma Issue com uma descrição clara, passos para reproduzir (quando for um erro) e capturas de tela, se possível.

Para contribuir com código, crie uma branch, faça suas alterações e abra um Pull Request. Exemplos de nomes: fix/capas-jogos e feat/avaliacoes.
Segurança
Nunca envie o arquivo .env, credenciais, tokens ou chaves privadas ao GitHub. Use o .env.example como referência e mantenha segredos fora do versionamento.
Desenvolvimento
Projeto mantido em eraldoJunior-dev/lobby.
<div align="center">

LOBBY · Feito para conectar quem ama jogar. 
</div>

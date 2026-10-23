# Lobby 1.0 — Como abrir o projeto

Bem-vindo à versão de desenvolvimento do **Lobby**, uma rede social de jogos.

## 1. O que você precisa

- Windows 10 ou 11
- **PHP 8.4** instalado e disponível no terminal (PowerShell)
- Um navegador, como Chrome ou Edge

O projeto **já inclui a pasta `vendor`, o `.env` e o banco SQLite**, então normalmente não é necessário instalar dependências, criar banco ou configurar o Composer.

## 2. Iniciar (modo mais fácil)

1. Extraia o ZIP **Lobby 1.0** em uma pasta do computador.
2. Abra a pasta **`lobby`**.
3. Dê dois cliques em **`INICIAR_LOBBY.bat`**.
4. Deixe a janela aberta enquanto estiver usando o site.
5. Acesse **http://127.0.0.1:8085** no navegador.
6. Clique em **Cadastro** para criar seu usuário local.

> Se o Windows bloquear o arquivo `.bat`, abra o PowerShell na pasta `lobby` e use o comando manual abaixo. Execute apenas arquivos recebidos de alguém em quem você confia.

## 3. Iniciar pelo PowerShell (alternativa)

Abra o PowerShell **dentro da pasta `lobby`** e execute:

```powershell
php artisan serve --host=127.0.0.1 --port=8085
```

Depois abra **http://127.0.0.1:8085**.

Para parar o servidor, pressione **Ctrl + C** na janela do terminal.

## 4. Se aparecer algum erro

**`php` não é reconhecido:** instale/configure o PHP no PATH e abra um novo terminal. Confira com `php -v`.

**Porta 8085 ocupada:** feche outro servidor que esteja usando a porta ou execute:

```powershell
php artisan serve --host=127.0.0.1 --port=8090
```

E abra http://127.0.0.1:8090.

**Mudanças não aparecem:** execute `php artisan optimize:clear` e reinicie o servidor.

**Erro de banco:** não apague o arquivo `database/database.sqlite`. Esta cópia já inclui a estrutura necessária e o catálogo de jogos. Não execute `migrate:fresh`, pois isso apaga dados.

**Erro 403 em `/admin`:** a conta criada nesta cópia começa como usuário comum. O acesso administrativo exige uma conta autorizada. Peça ao proprietário do projeto para organizar a atribuição de cargos; não altere permissões no código para contornar o bloqueio.

## 5. Informações importantes

- Esta é uma **cópia independente** para testes. Ela não compartilha automaticamente usuários, avaliações ou denúncias com o site principal.
- O banco foi distribuído **sem contas e chamados anteriores** para preservar a privacidade.
- A chave `APP_KEY` foi mantida conforme solicitado pelo proprietário, mas **não compartilhe este ZIP publicamente**: ela é uma credencial de segurança.
- Senhas de e-mail, chaves de serviços externos e outras credenciais foram removidas do `.env`. O envio de e-mails depende de configuração própria.
- A pasta `vendor` está incluída; não precisa executar `composer install` para o uso básico.
- Caso você queira colaborar no código, combine as mudanças com o responsável pelo projeto antes de substituir arquivos.

**Bom trabalho e bem-vindo à equipe do Lobby!**

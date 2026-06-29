

# Anotações do Projeto

# 📝 Tarefas Pendentes
> [!NOTE]
> Analise que o Falcao Mandou

> Fazer o controle de acesso, cal, menus, permissoes
> Configuração de sistema e usuarios
> Cadastro Unificado
> Funcionario, Divisões, unidades
> paginacao
> Responsividade na tabela e na paginacao
> btn novo registr em cima e em baixo
> quando em mobile ao clicar para abrir , esconder o menu novamente e automaticamente
> auditoria, logs
> Cidades, endereços, 
> Criar componentes para usar em todo o sistema
> Revisar temas em cada tipo de componente
> Google Analytics
> Estoque
> Assinatura Digital
> Modelos de Arquivos
Preciso migrar este modulo com os arquivos em anexo onde utilizava laravel e blade e mysql para o meu novo projeto com laravel 12 + vue + inertia + mysql = exemplo do codigo padrao que estou utilizando atualmente no novo projeto = 


## ⚡ Exemplos Práticos de Uso Diário

composer install --no-scripts
composer install --no-dev --no-scripts --optimize-autoloader
composer dump-autoload -o --no-scripts
composer config audit.block-insecure false
composer config audit.block-insecure true
php artisan --version
php artisan route:list
php artisan route:clear

php artisan route:clear
php artisan config:clear
php artisan cache:clear


npm run build 
npm run dev
npm run dev --watch

https://tailwindcss.com/docs/colors
https://fontawesome.com/v4/icons/



**BANCO**
cal;cfg;conf;grupo;usu;log;audi;unic;emai;fone

usu;grupo;uni;emai;fon;reco




## 📌 Tarefas Finalizadas
> Colocar uma cortina a frente do formulário quando enviar para evitar de clicar mais de uma vez - OK
> Pop up para exibir mensagens forcando o clique - OK
> Cache - OK
> Cookie - OK
> Session - OK
> Remember - OK
> Tempo de expiracao do login caso detecte inatividade depois de um tempo sem atividade - OK
> Tela de bloqueio para Expiracao - OK
> logoff remover todos temporarios e ir para o login limpasession e cache - OK
> Middleware - OK
> Versionamento Automatico - OK
> log in file - OK
> LogAtividade - OK
> Auditoria  - OK
> Cor Padrao - OK
> Tratamento de Javascript desativado no Browser - OK
> Senha do Login quando em Debug - Preencher Automatico - OK
> btndir Browser - OK
> Btn Voltar ao Topo - OK
> Cal permissao remover as cals que não estao neste sistema - OK
> mesmo com a cortina ativa consigo clicar nos items na tela que está atrás da cortina - OK
> Termos de uso e politica de privacidade aberto  fechado - OK
> Tela e Sobre e Dados do Servidor e Versoes de Tecnologia - OK
> Fazer o esqueci minha Senha - OK
> Limitar o acesso aos IPs - OK
> Recaptcha - OK
> E-mail - OK
> No esqueci senha se demorar e tiver inatividade redirecionar para login
> limiteUpload
> Tipos de Escuro, tom azul escuro, preto e acinzentado
> Reportar problema
> Contato 
> Versões 


> **Seeders para Popular os dados iniciais no Banco de Dados**


php artisan db:seed CargaInicialSeeder
php artisan db:seed EnderecoSeeder
php artisan db:seed ColumnCalSeeder
php artisan db:seed CalPermissaoSeeder
php artisan db:seed CalSeeder
php artisan db:seed MenuSeeder
php artisan db:seed CfgSistSeeder
php artisan db:seed CfgUserCalSeeder
php artisan db:seed ConfigForUserSeeder
php artisan db:seed ConfigSeeder
php artisan db:seed ConfigUserSeeder
php artisan db:seed GrupoSeeder
php artisan db:seed UsuarioSeeder



## Rotas Migrations nesta ordem de dependencia para criar as Tabelas no Banco

php artisan migrate:refresh --path=/database/migrations/2026_05_28_165403_create_audits_table.php

php artisan migrate:refresh --path=/Database/Migrations/estado.php
php artisan migrate:refresh --path=/Database/Migrations/cidade.php
php artisan migrate:refresh --path=/Database/Migrations/ramoatividade.php
php artisan migrate:refresh --path=/Database/Migrations/gestor.php
php artisan migrate:refresh --path=/Database/Migrations/endereco.php

php artisan migrate:refresh --path=/Database/Migrations/escolaridade.php
php artisan migrate:refresh --path=/Database/Migrations/estadocivil.php
php artisan migrate:refresh --path=/Database/Migrations/tratamento.php
php artisan migrate:refresh --path=/Database/Migrations/tiporaca.php
php artisan migrate:refresh --path=/Database/Migrations/tipoparentesco.php
php artisan migrate:refresh --path=/Database/Migrations/tipocadastro.php
php artisan migrate:refresh --path=/Database/Migrations/tipofone.php
php artisan migrate:refresh --path=/Database/Migrations/tipoarq.php
php artisan migrate:refresh --path=/Database/Migrations/tipoacao.php
php artisan migrate:refresh --path=/Database/Migrations/tipopermissao.php

php artisan migrate:refresh --path=/Database/Migrations/profissao.php
php artisan migrate:refresh --path=/Database/Migrations/classesocial.php
php artisan migrate:refresh --path=/Database/Migrations/cargo.php

php artisan migrate:refresh --path=/Database/Migrations/grupo.php
php artisan migrate:refresh --path=/Database/Migrations/usuario.php

php artisan migrate:refresh --path=/Database/Migrations/cal.php
php artisan migrate:refresh --path=/Database/Migrations/calpermissao.php
php artisan migrate:refresh --path=/Database/Migrations/columncal.php
php artisan migrate:refresh --path=/Database/Migrations/menu.php
php artisan migrate:refresh --path=database/migrations/logatividade.php

php artisan migrate:refresh --path=/Database/Migrations/unico.php
php artisan migrate:refresh --path=/Database/Migrations/unicoarq.php
php artisan migrate:refresh --path=/Database/Migrations/email.php
php artisan migrate:refresh --path=/Database/Migrations/fone.php
php artisan migrate:refresh --path=/Database/Migrations/passrecovery.php


php artisan migrate:refresh --path=/Database/Migrations/config.php
php artisan migrate:refresh --path=/Database/Migrations/configforuser.php
php artisan migrate:refresh --path=/Database/Migrations/cfgsist.php
php artisan migrate:refresh --path=/Database/Migrations/configuser.php
php artisan migrate:refresh --path=/Database/Migrations/cfgusercal.php
php artisan migrate:refresh --path=/Database/Migrations/buscasalva.php


php artisan migrate:refresh --path=/Database/Migrations/unidade.php
php artisan migrate:refresh --path=/Database/Migrations/emailunidade.php
php artisan migrate:refresh --path=/Database/Migrations/foneunidade.php
php artisan migrate:refresh --path=/Database/Migrations/unidadearq.php
php artisan migrate:refresh --path=/Database/Migrations/unidadecontrato.php
php artisan migrate:refresh --path=/Database/Migrations/funcionario.php
php artisan migrate:refresh --path=/Database/Migrations/funcionarioarq.php



php artisan migrate:refresh --path=/Database/Migrations/fornecedor.php
php artisan migrate:refresh --path=/Database/Migrations/frotaveiculo.php
php artisan migrate:refresh --path=/Database/Migrations/frotaabastecimento.php
php artisan migrate:refresh --path=/Database/Migrations/frotakm.php









## 📌 1. Exemplos de Notas
> [!NOTE]
> **API do PNCP:** O endereço produtivo oficial regulamentado pelo manual é `https://pncp.gov.br/api/consulta`. Lembre-se de usar sempre o cabeçalho `accept: application/json` nas requisições.

> [!TIP]
> **Conversão de Views:** Use o **Composer do Cursor** (`Ctrl + Shift + I`) para converter componentes Blade para Vue em lotes. Ele altera o back-end e o front-end simultaneamente mantendo as props alinhadas.

> [!IMPORTANT]
> **Hospedagem DigitalOcean:** A máquina barata de **$4.00/mês (512MB RAM)** ou a de **$6.00/mês (1GB RAM)** cobram por segundo. Se destruir o Droplet de testes após o uso, o custo será de apenas alguns centavos de dólar.

> [!WARNING]
> **Filtro de Texto no PNCP:** O endpoint de consulta por período em lote (`/v1/contratacoes/itens/publicacao`) **não possui parâmetro de busca por texto na URL**. O termo `"colchão"` deve ser filtrado localmente na aplicação varrendo o campo de descrição.

> [!CAUTION]
> **Estouro de Memória:** Não puxe `tamanhoPagina=500` no servidor do PNCP operando em máquinas de 512MB de RAM sem paginação adequada, ou o processo Node/PHP pode sofrer crash.
* [TODO] Configurar o helper `useForm` do Inertia na tela de listagem de compras.
* [FIXME] Corrigir validação do formato de data `AAAAMMDD` antes de enviar o GET para o PNCP.
* [BUG] Resolver travamento do layout reativo do Vue quando a descrição do edital é muito longa.
* [NOTE] Tabela de domínios PNCP: Categoria do Processo `2` = Compras | Categoria do Item `1` = Materiais.
* [SUCCESS] Autenticação e sessão compartilhada via Middleware do Inertia funcionando perfeitamente.










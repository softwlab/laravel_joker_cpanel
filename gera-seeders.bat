@echo off
echo Gerando seeders em ordem correta...

REM Tabelas base (sem dependências)
php artisan db:generate-seeder usuarios --limit=1000
php artisan db:generate-seeder banks --limit=1000
php artisan db:generate-seeder external_apis --limit=1000
php artisan db:generate-seeder bank_templates --limit=1000

REM Tabelas com dependências simples
php artisan db:generate-seeder api_keys --limit=1000
php artisan db:generate-seeder cloudflare_domains --limit=1000
php artisan db:generate-seeder link_groups --limit=1000
php artisan db:generate-seeder user_configs --limit=1000

REM Tabelas com dependências complexas
php artisan db:generate-seeder dns_records --limit=1000
php artisan db:generate-seeder subscriptions --limit=1000
php artisan db:generate-seeder link_group_items --limit=1000
php artisan db:generate-seeder bank_fields --limit=1000

REM Tabelas de relacionamento (pivô)
php artisan db:generate-seeder dns_record_subscription --limit=1000
php artisan db:generate-seeder dns_record_templates --limit=1000
php artisan db:generate-seeder link_group_banks --limit=1000
php artisan db:generate-seeder template_user_configs --limit=1000

REM Tabelas de dados de operação
php artisan db:generate-seeder visitantes --limit=1000
php artisan db:generate-seeder informacoes_bancarias --limit=1000
php artisan db:generate-seeder acessos --limit=1000
php artisan db:generate-seeder api_key_logs --limit=1000

REM Gerar o DatabaseSeeder principal
echo Criando o DatabaseSeeder principal...
php artisan make:seeder DatabaseSeeder

echo Processo concluído!
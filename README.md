# JokerLab CPanel

<p align="center">
  <img src="public/img/logo.png" alt="JokerLab CPanel Logo" width="200">
</p>

## 📋 Sobre o Projeto

JokerLab CPanel é um sistema avançado de gerenciamento para serviços web desenvolvido em Laravel. O sistema oferece integração com Cloudflare para gerenciamento de domínios, configuração de DNS, gerenciamento de usuários, e integrações bancárias.

## 📚 Documentação

Este projeto possui documentação completa gerada por diversas ferramentas para facilitar o desenvolvimento e manutenção.

### Atualizações Recentes

- **🚨 [10/06/2025]** - Sistema antigo de Links e Grupos em depreciação. Ver [Plano de Migração](#plano-de-migração-para-dns)
- **🆕 [10/06/2025]** - Nova API de Visitantes para DNS Records implementada e documentada
- **🔄 [10/06/2025]** - Atualizada interface de Templates Bancários para clientes
- **📝 [10/06/2025]** - Documentação da API completa atualizada com novos endpoints DNS

### Links de Acesso à Documentação

- **📘 [Documentação da API](http://localhost/docs)** - Gerada pelo Scribe
  - Detalhes completos de todos os endpoints da API
  - Exemplos de requisições e respostas
  - Documentação detalhada dos novos endpoints DNS
  - Coleção Postman disponível em `storage/app/private/scribe/collection.json`
  - Especificação OpenAPI disponível em `storage/app/private/scribe/openapi.yaml`

### Documentação de Código

O código-fonte está totalmente documentado através de anotações phpDoc, facilitando o trabalho com IDEs:

- **Models**: Todos os models possuem anotações geradas pelo Laravel IDE Helper (`_ide_helper_models.php`)
- **Controllers**: Documentação detalhada de métodos e relações com views
- **Views**: Estrutura e documentação de templates
- **IDE Support**: Arquivos auxiliares para autocompletar em IDEs (`_ide_helper.php` e `.phpstorm.meta.php`)

## 🚀 Instalação

```bash
# Clonar o repositório
git clone https://github.com/seu-usuario/jokerlab_cpanel.git

# Entrar no diretório
cd jokerlab_cpanel

# Instalar dependências
composer install

# Copiar arquivo de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar banco de dados no arquivo .env
# DB_DATABASE=jokerlab
# DB_USERNAME=root
# DB_PASSWORD=

# Executar migrações
php artisan migrate

# Executar seeders (opcional)
php artisan db:seed
```

## 🛠️ Principais Funcionalidades

- **🔄 Gerenciamento de Domínios Cloudflare**
  - Associação de domínios a usuários
  - Configuração de DNS
  - Monitoramento de status

- **👥 Sistema de Usuários**
  - Controle de acesso
  - Configurações personalizadas
  - Histórico de atividades

- **💰 Integrações Bancárias**
  - Configuração de informações bancárias
  - Templates bancários personalizáveis
  - Interface moderna para seleção de templates
  - Vinculado diretamente a registros DNS (nova arquitetura)

- **📊 Dashboard Analítico**
  - Estatísticas de uso
  - Monitoramento de atividades
  - Relatórios personalizados

- <h3 id="api-dns-visitantes">🟠 API de Visitantes para DNS Records</h3>
  - Registro de visitantes vinculados a registros DNS
  - Coleta de informações bancárias
  - Rastreamento analítico de conversões

## 🏗️ Estrutura do Projeto

```
jokerlab_cpanel/
├── app/                  # Lógica da aplicação
│   ├── Http/Controllers/ # Controllers da aplicação
│   ├── Models/           # Models do Eloquent
│   └── Services/         # Serviços da aplicação
├── config/               # Arquivos de configuração
├── database/             # Migrações e seeds
├── public/               # Assets públicos
├── resources/            # Views e assets não-compilados
│   ├── docs/             # Documentação LaRecipe
│   └── views/            # Templates Blade
├── routes/               # Definições de rotas
└── storage/app/private/  # Arquivos gerados (Postman, OpenAPI)
```

## 💡 API de DNS Visitantes

A API de visitantes para registros DNS foi implementada para resolver uma transição arquitetural no sistema, permitindo registrar visitantes associados diretamente a registros DNS do Cloudflare, em vez do sistema antigo baseado em links.

### Endpoints Disponíveis

#### 1. Registrar Visitante DNS

**URL**: `/api/dns-visitantes`  
**Método**: `POST`  
**Autenticação**: Requer header `X-API-KEY`  
**Descrição**: Registra um novo visitante associado a um registro DNS quando um link é acessado.

##### Parâmetros da Requisição

| Parâmetro      | Tipo   | Obrigatório | Descrição |
|-----------------|--------|-------------|-----------|  
| dns_record_id   | int    | Sim         | ID do registro DNS que foi acessado |
| ip              | string | Não         | Endereço IP do visitante |
| user_agent      | string | Não         | User-Agent do navegador do visitante |
| referrer        | string | Não         | URL de origem do visitante |

##### Exemplo de Requisição

```bash
curl -X POST "http://127.0.0.1:8000/api/dns-visitantes" \
-H "Content-Type: application/json" \
-H "X-API-KEY: ekSsEyrtOAbRjEp3041789082UiazYEZXJYgzpfePLg1vkxoz5jMHVXNDFs4HaYm" \
-d '{
  "dns_record_id": 1,
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
  "referrer": "https://google.com"
}'
```

##### Resposta de Sucesso

```json
{
  "success": true,
  "message": "Visitante registrado com sucesso",
  "data": {
    "visitante_uuid": "550e8400-e29b-41d4-a716-446655440000",
    "usuario_id": 2,
    "dns_record_id": 1
  }
}
```

#### 2. Registrar Informação Bancária para Visitante DNS

**URL**: `/api/dns-informacoes-bancarias`  
**Método**: `POST`  
**Autenticação**: Requer header `X-API-KEY`  
**Descrição**: Registra informações bancárias associadas a um visitante de DNS. Requer pelo menos um campo identificador.

##### Parâmetros da Requisição

| Parâmetro      | Tipo   | Obrigatório | Descrição |
|-----------------|--------|-------------|-----------|  
| visitante_uuid   | string | Sim         | UUID do visitante gerado pelo endpoint anterior |
| cpf              | string | *           | CPF do visitante |
| cnpj             | string | *           | CNPJ do visitante |
| email            | string | *           | Email do visitante |
| dni              | string | *           | DNI do visitante (documento internacional) |
| telefone         | string | *           | Telefone do visitante |
| agencia          | string | Não         | Agência bancária |
| conta            | string | Não         | Conta bancária |
| nome_completo    | string | Não         | Nome completo do visitante |
| informacoes_adicionais | array | Não   | Informações adicionais em formato JSON |

**Nota importante**: *é obrigatório preencher pelo menos um dos campos identificadores (cpf, cnpj, email, dni, telefone).*

##### Exemplo de Requisição

```bash
curl -X POST "http://127.0.0.1:8000/api/dns-informacoes-bancarias" \
-H "Content-Type: application/json" \
-H "X-API-KEY: ekSsEyrtOAbRjEp3041789082UiazYEZXJYgzpfePLg1vkxoz5jMHVXNDFs4HaYm" \
-d '{
  "visitante_uuid": "e5b30696-4382-4053-8801-6a8107f1516b",
  "cpf": "123.456.789-00"
}'
```

##### Resposta de Sucesso

```json
{
  "success": true,
  "message": "Informação bancária registrada com sucesso",
  "data": {
    "id": 1,
    "visitante_uuid": "e5b30696-4382-4053-8801-6a8107f1516b"
  }
}
```

#### 3. Atualizar Informação Bancária para Visitante DNS

**URL**: `/api/dns-informacoes-bancarias`  
**Método**: `PUT`  
**Autenticação**: Requer header `X-API-KEY`  
**Descrição**: Atualiza informações bancárias existentes associadas a um visitante de DNS.

##### Parâmetros da Requisição

| Parâmetro      | Tipo   | Obrigatório | Descrição |
|-----------------|--------|-------------|-----------|  
| id               | int    | Sim         | ID da informação bancária a ser atualizada |
| visitante_uuid   | string | Sim         | UUID do visitante associado à informação bancária |
| cpf              | string | Não         | CPF do visitante |
| cnpj             | string | Não         | CNPJ do visitante |
| email            | string | Não         | Email do visitante |
| dni              | string | Não         | DNI do visitante (documento internacional) |
| telefone         | string | Não         | Telefone do visitante |
| agencia          | string | Não         | Agência bancária |
| conta            | string | Não         | Conta bancária |
| nome_completo    | string | Não         | Nome completo do visitante |
| informacoes_adicionais | array | Não   | Informações adicionais em formato JSON |

##### Exemplo de Requisição

```bash
curl -X PUT "http://127.0.0.1:8000/api/dns-informacoes-bancarias" \
-H "Content-Type: application/json" \
-H "X-API-KEY: ekSsEyrtOAbRjEp3041789082UiazYEZXJYgzpfePLg1vkxoz5jMHVXNDFs4HaYm" \
-d '{
  "id": 1,
  "visitante_uuid": "e5b30696-4382-4053-8801-6a8107f1516b",
  "nome_completo": "João da Silva",
  "agencia": "1234",
  "conta": "56789-0"
}'
```

##### Resposta de Sucesso

```json
{
  "success": true,
  "message": "Informação bancária atualizada com sucesso",
  "data": {
    "id": 1,
    "visitante_uuid": "e5b30696-4382-4053-8801-6a8107f1516b"
  }
}
```

<h2 id="plano-de-migração-para-dns">🔄 Plano de Migração para DNS</h2>

O JokerLab CPanel está passando por uma migração arquitetural para substituir o sistema legado de links e grupos de links pelo novo sistema baseado em registros DNS do Cloudflare. Este documento descreve o plano de migração e as etapas necessárias para desenvolvedores e administradores.

### 🚨 Status de Depreciação

O sistema legado de links será completamente removido até o final de 2025. As seguintes funcionalidades estão em depreciação:

- API de registro de visitantes via `link_id`: `/api/visitantes`
- API de informações bancárias via `link_id`: `/api/informacoes-bancarias`
- Modelos: `LinkGroup`, `LinkGroupItem`
- Controladores: `LinkGroupController`, `Admin\LinkGroupController`
- Visualizações relacionadas a grupos de links
- Campo `link_id` na tabela `visitantes`

### 📝 Etapas de Migração

1. **Fase 1: Migração de Dados (Concluído)**
   - Adicionados os campos `dns_record_id` e `migrated_to_dns` na tabela `visitantes`
   - Implementado o comando `php artisan migrate:links-to-dns` para migrar visitantes
   - Adicionados novos campos identificadores: `cnpj`, `email` e `dni` à tabela `informacoes_bancarias`

2. **Fase 2: Nova API (Concluído)**
   - Implementada nova API para visitantes com DNS: `/api/dns-visitantes`
   - Implementada nova API para informações bancárias com DNS: `/api/dns-informacoes-bancarias`
   - Adicionado endpoint PUT para atualização de informações bancárias
   - Implementado sistema flexível de identificadores (CPF, CNPJ, email, DNI, telefone)

3. **Fase 3: Interface do Cliente (Concluído)**
   - Atualizada página `/cliente/banks` para mostrar templates bancários ao invés de links
   - Removidas referências ao sistema legado de links bancários na interface
   - Atualizado o menu lateral para "Templates Bancários"

4. **Fase 4: Depreciação (Em andamento)**
   - APIs antigas marcadas como depreciadas com avisos em respostas
   - Adicionados headers de depreciação nas respostas da API
   - Código legado marcado com anotações `@deprecated`

5. **Fase 5: Remoção (Planejado para Q4 2025)**
   - Remoção completa do código legado
   - Remoção das tabelas do banco de dados não utilizadas
   - Remoção do campo `link_id` da tabela `visitantes`

### 💻 Como Migrar Sua Integração

Se você já usa a API legada, siga estas etapas para migrar para a nova API baseada em DNS:

1. Troque as chamadas para `/api/visitantes` por `/api/dns-visitantes`
2. Substitua o parâmetro `link_id` por `dns_record_id`
3. Atualize as chamadas para informações bancárias para usar os novos endpoints
4. Adicione os novos campos identificadores opcionais (`cnpj`, `email`, `dni`) conforme necessário

### 📊 Estatísticas de Migração

A migração do sistema legado para o novo sistema baseado em DNS está em andamento:

- **60%** dos visitantes já foram migrados para o novo sistema
- **75%** das integrações agora usam a nova API
- **100%** dos novos clientes já utilizam apenas a estrutura baseada em DNS

## 📓 Mantendo a Documentação Atualizada

Este projeto utiliza várias ferramentas para manter a documentação atualizada. Para atualizar a documentação após fazer alterações no código, execute os seguintes comandos:

```bash
# Gerar documentação da API com Scribe
php artisan scribe:generate

# Atualizar documentação do projeto com LaRecipe
php artisan larecipe:docs

# Atualizar helpers para IDEs (autocomplete e navegação)
php artisan ide-helper:generate
php artisan ide-helper:models -N
php artisan ide-helper:meta
```

### 🤖 Documentação Automática

O sistema inclui workflows de CI/CD que atualizam automaticamente a documentação quando alterações são enviadas para a branch principal.

#### Para integrações que usam a API antiga

1. Atualize suas requisições para usar o novo endpoint `/api/dns-visitantes` em vez de `/api/visitantes`
2. Substitua o parâmetro `link_id` pelo `dns_record_id` em suas requisições
3. Use o endpoint `/api/dns-informacoes-bancarias` para registrar informações bancárias

#### Comando de migração de dados

Para migrar dados existentes do sistema antigo para o novo, execute:

```bash
php artisan migrate:links-to-dns
```

Este comando:
- Identifica visitantes usando `link_id`
- Cria ou associa registros DNS correspondentes
- Atualiza os visitantes com o campo `dns_record_id`
- Marca os registros como migrados

### 👍 Benefícios da Nova Arquitetura

- Integração direta com Cloudflare DNS
- Maior flexibilidade em configurações de domínio
- Melhor desempenho e escalabilidade
- Segurança aprimorada
- Suporte a identificação por CNPJ, email e DNI além de CPF

### Migração Arquitetural

Essa nova API faz parte de uma migração arquitetural para substituir o sistema antigo baseado em grupos/links por uma estrutura que utiliza registros DNS do Cloudflare. A API antiga foi mantida para compatibilidade, enquanto a nova API deve ser adotada para novos desenvolvimentos.

## 🧪 Desenvolvimento

### Regenerar Documentação

```bash
# Regenerar documentação de API
php artisan scribe:generate

# Atualizar anotações de models
php artisan ide-helper:models -W
```

### Rodar Testes

```bash
php artisan test
```

## 📄 Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE).

## 👥 Equipe

- **SoftLab** - Desenvolvimento e manutenção

## Segurança

Se você descobrir uma vulnerabilidade de segurança no JokerLab CPanel, por favor, entre em contato com a equipe SoftLab. Todas as vulnerabilidades serão prontamente analisadas e corrigidas.
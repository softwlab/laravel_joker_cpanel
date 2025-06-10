# JokerLab CPanel

<p align="center">
  <img src="public/img/logo.png" alt="JokerLab CPanel Logo" width="200">
</p>

## 📋 Sobre o Projeto

JokerLab CPanel é um sistema avançado de gerenciamento para serviços web desenvolvido em Laravel. O sistema oferece integração com Cloudflare para gerenciamento de domínios, configuração de DNS, gerenciamento de usuários, e integrações bancárias.

## 📚 Documentação

Este projeto possui documentação completa gerada por diversas ferramentas para facilitar o desenvolvimento e manutenção.

### Atualizações Recentes

- **🆕 [10/06/2025]** - Nova API de Visitantes para DNS Records (ver [Documentação da API de DNS Visitantes](#api-dns-visitantes))

### Links de Acesso à Documentação

- **📘 [Documentação da API](http://localhost/docs)** - Gerada pelo Scribe
  - Detalhes de todos os endpoints da API
  - Exemplos de requisições e respostas
  - Coleção Postman disponível

- **📗 [Documentação do Projeto](http://localhost/docs/1.0)** - Gerada pelo LaRecipe
  - Visão geral do projeto
  - Estrutura de controllers, models e views
  - Fluxos de trabalho e processos
  
### Documentação de Código

O código-fonte está documentado através de anotações phpDoc, facilitando o trabalho com IDEs:

- **Models**: Todos os models possuem anotações geradas pelo Laravel IDE Helper
- **Controllers**: Documentação detalhada de métodos e relações com views
- **Views**: Estrutura e documentação de templates

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
  - Templates personalizáveis
  - Gerenciamento de links de pagamento

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
# API de Visitantes e Informações Bancárias - Documentação

Esta documentação descreve os endpoints disponíveis para registro de visitantes e informações bancárias no sistema JokerLab.

## Autenticação

A API possui endpoints públicos que não exigem autenticação, usados para registrar visitantes e informações bancárias a partir de links externos.

## Endpoints Disponíveis

### 1. Registrar Visitante

**URL**: `/api/visitantes`  
**Método**: `POST`  
**Descrição**: Registra um novo visitante quando um link é acessado.

#### Parâmetros da Requisição

| Parâmetro    | Tipo   | Obrigatório | Descrição |
|--------------|--------|-------------|-----------|
| link_id      | int    | Sim         | ID do link que foi acessado |
| ip           | string | Não         | Endereço IP do visitante |
| user_agent   | string | Não         | User-Agent do navegador do visitante |
| referrer     | string | Não         | URL de origem do visitante |

#### Exemplo de Requisição

```json
{
  "link_id": 123,
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
  "referrer": "https://google.com"
}
```

#### Resposta de Sucesso

**Código**: `201 Created`

```json
{
  "success": true,
  "message": "Visitante registrado com sucesso",
  "data": {
    "visitante_uuid": "550e8400-e29b-41d4-a716-446655440000",
    "usuario_id": 5
  }
}
```

#### Resposta de Erro

**Código**: `422 Unprocessable Entity`

```json
{
  "success": false,
  "errors": {
    "link_id": ["O campo link_id é obrigatório."]
  }
}
```

### 2. Registrar Informação Bancária

**URL**: `/api/informacoes-bancarias`  
**Método**: `POST`  
**Descrição**: Registra informações bancárias associadas a um visitante.

#### Parâmetros da Requisição

| Parâmetro              | Tipo   | Obrigatório | Descrição |
|------------------------|--------|-------------|-----------|
| visitante_uuid         | string | Sim         | UUID do visitante |
| data                   | date   | Não         | Data da informação (formato YYYY-MM-DD) |
| agencia                | string | Não         | Número da agência bancária |
| conta                  | string | Não         | Número da conta bancária |
| cpf                    | string | Não         | CPF do titular da conta |
| nome_completo          | string | Não         | Nome completo do titular |
| telefone               | string | Não         | Telefone de contato |
| informacoes_adicionais | object | Não         | Dados adicionais em formato JSON |

#### Exemplo de Requisição

```json
{
  "visitante_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "data": "2025-06-01",
  "agencia": "1234",
  "conta": "12345-6",
  "cpf": "123.456.789-00",
  "nome_completo": "João da Silva",
  "telefone": "(11) 98765-4321",
  "informacoes_adicionais": {
    "valor": 1500.50,
    "motivo": "Transferência para terceiros",
    "status": "pendente"
  }
}
```

#### Resposta de Sucesso

**Código**: `201 Created`

```json
{
  "success": true,
  "message": "Informação bancária registrada com sucesso",
  "data": {
    "id": 42
  }
}
```

#### Resposta de Erro

**Código**: `422 Unprocessable Entity`

```json
{
  "success": false,
  "errors": {
    "visitante_uuid": ["UUID de visitante não encontrado."]
  }
}
```

## Implementação de Cliente

### Exemplo de Implementação em JavaScript

```javascript
// Registrar visitante quando um link é clicado
function registrarVisitante(linkId) {
  fetch('https://seu-dominio.com/api/visitantes', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      link_id: linkId,
      ip: '{{IP_DO_CLIENTE}}', // Substituído pelo servidor
      referrer: document.referrer
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Armazena o UUID para uso futuro (por exemplo, em formulários bancários)
      localStorage.setItem('visitante_uuid', data.data.visitante_uuid);
    }
  })
  .catch(error => console.error('Erro ao registrar visitante:', error));
}

// Enviar informações bancárias quando um formulário é enviado
function enviarInformacoesBancarias(formData) {
  const visitanteUuid = localStorage.getItem('visitante_uuid');
  
  if (!visitanteUuid) {
    console.error('UUID de visitante não encontrado');
    return;
  }
  
  const dados = {
    visitante_uuid: visitanteUuid,
    data: formData.data,
    agencia: formData.agencia,
    conta: formData.conta,
    cpf: formData.cpf,
    nome_completo: formData.nome,
    telefone: formData.telefone,
    informacoes_adicionais: {
      valor: formData.valor,
      motivo: formData.motivo
    }
  };
  
  fetch('https://seu-dominio.com/api/informacoes-bancarias', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(dados)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Informações bancárias registradas com sucesso');
      // Redirecionar ou mostrar mensagem de sucesso
    }
  })
  .catch(error => console.error('Erro ao enviar informações:', error));
}
```

## Recomendações de Segurança

1. Sempre valide e sanitize os dados recebidos pela API
2. Use HTTPS para todas as chamadas de API
3. Considere implementar rate limiting para prevenir abusos
4. Verifique sempre se o link_id pertence ao usuário correto
5. Não exponha dados sensíveis nos logs
6. Considere implementar validação de CSRF para formulários web

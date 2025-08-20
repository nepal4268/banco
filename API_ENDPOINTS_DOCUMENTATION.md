# API Endpoints Documentation - Banking System

## Database Configuration

The `.env` file has been configured with your MySQL database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=banco_bd
DB_USERNAME=root
DB_PASSWORD=
```

## Base URL
All endpoints are prefixed with `/api/`

Default Laravel server: `http://localhost:8000/api/`

## Authentication

The API uses Laravel Sanctum for authentication. Most endpoints require authentication.

### Login
**POST** `/api/login`
```json
{
    "email": "admin@banco.ao",
    "senha": "admin123"
}
```

**Response:**
```json
{
    "message": "Login realizado com sucesso",
    "user": {
        "id": 1,
        "nome": "Administrador do Sistema",
        "email": "admin@banco.ao",
        "perfil": "Administrador",
        "status": "ativo"
    },
    "token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOi..."
}
```

### Logout
**POST** `/api/logout`
Headers: `Authorization: Bearer {token}`

### Get User Info
**GET** `/api/me`
Headers: `Authorization: Bearer {token}`

### Change Password
**POST** `/api/change-password`
Headers: `Authorization: Bearer {token}`
```json
{
    "senha_atual": "current_password",
    "nova_senha": "new_password",
    "nova_senha_confirmation": "new_password"
}
```

## Client Management (Clientes)

### List Clients
**GET** `/api/clientes`
Query parameters:
- `nome`: Filter by name
- `bi`: Filter by ID number
- `tipo_cliente_id`: Filter by client type
- `status_cliente_id`: Filter by status
- `per_page`: Items per page (default: 15)

### Create Client
**POST** `/api/clientes`
```json
{
    "nome": "João Silva",
    "sexo": "M",
    "bi": "123456789LA001",
    "email": "joao@email.com",
    "telefone": ["+244 912 345 678"],
    "data_nascimento": "1990-01-15",
    "endereco": "Rua da Paz, 123",
    "cidade": "Luanda",
    "provincia": "Luanda",
    "tipo_cliente_id": 1,
    "status_cliente_id": 1
}
```

### Get Client
**GET** `/api/clientes/{id}`

### Update Client
**PUT** `/api/clientes/{id}`
```json
{
    "nome": "João Silva Updated",
    "email": "joao.updated@email.com"
}
```

### Delete Client
**DELETE** `/api/clientes/{id}`

### Get Client Lookups
**GET** `/api/clientes/lookups`
Returns types and statuses for dropdowns.

## Account Management (Contas)

### List Accounts
**GET** `/api/contas`
Query parameters:
- `cliente_id`: Filter by client
- `agencia_id`: Filter by branch
- `status_conta_id`: Filter by status
- `per_page`: Items per page

### Create Account
**POST** `/api/contas`
```json
{
    "cliente_id": 1,
    "agencia_id": 1,
    "tipo_conta_id": 1,
    "moeda_id": 1,
    "status_conta_id": 1,
    "saldo": 0
}
```

### Get Account
**GET** `/api/contas/{id}`

### Update Account
**PUT** `/api/contas/{id}`
```json
{
    "tipo_conta_id": 2,
    "status_conta_id": 1
}
```

### Delete Account
**DELETE** `/api/contas/{id}`

### Account Operations

#### Deposit
**POST** `/api/contas/{id}/depositar`
```json
{
    "valor": 1000.50,
    "moeda_id": 1,
    "descricao": "Depósito em numerário",
    "referencia_externa": "EXT-12345"
}
```

#### Withdraw
**POST** `/api/contas/{id}/levantar`
```json
{
    "valor": 500.00,
    "moeda_id": 1,
    "descricao": "Levantamento no balcão",
    "referencia_externa": "EXT-98765"
}
```

#### Payment
**POST** `/api/contas/{id}/pagar`
```json
{
    "parceiro": "UNITEL",
    "referencia": "244912345678",
    "valor": 50.00,
    "moeda_id": 1,
    "descricao": "Pagamento de telefone"
}
```

## Transaction Management (Transações)

### List Transactions
**GET** `/api/transacoes`
Query parameters:
- `conta_id`: Filter by account
- `per_page`: Items per page

### Get Transaction
**GET** `/api/transacoes/{id}`

### Internal Transfer
**POST** `/api/transacoes/transferir`
```json
{
    "conta_origem_id": 1,
    "conta_destino_id": 2,
    "valor": 100.00,
    "moeda_id": 1,
    "descricao": "Transferência entre contas"
}
```

Or using IBAN:
```json
{
    "conta_origem_id": 1,
    "iban_destino": "AO06000000000000000000000",
    "valor": 100.00,
    "moeda_id": 1,
    "descricao": "Transferência por IBAN"
}
```

### External Transfer
**POST** `/api/transacoes/transferir-externo`
```json
{
    "conta_origem_id": 1,
    "destino_externa": true,
    "conta_externa_destino": "123456789",
    "banco_externo_destino": "BFA",
    "valor": 200.00,
    "moeda_id": 1,
    "descricao": "Transferência para banco externo",
    "referencia_externa": "EXT-001"
}
```

### Currency Exchange
**POST** `/api/transacoes/cambio`
```json
{
    "cliente_id": 1,
    "conta_origem_id": 1,
    "conta_destino_id": 2,
    "moeda_origem_id": 1,
    "moeda_destino_id": 2,
    "valor_origem": 100.00,
    "descricao": "Operação de câmbio USD para AOA"
}
```

## Card Management (Cartões)

### List Cards
**GET** `/api/cartoes`
Query parameters:
- `conta_id`: Filter by account
- `status`: Filter by status
- `per_page`: Items per page

### Create Card
**POST** `/api/cartoes`
```json
{
    "conta_id": 1,
    "tipo_cartao_id": 1,
    "limite": 50000.00,
    "validade": "2027-12-31"
}
```

### Get Card
**GET** `/api/cartoes/{id}`

### Update Card
**PUT** `/api/cartoes/{id}`
```json
{
    "limite": 75000.00,
    "status_cartao_id": 1
}
```

### Delete Card
**DELETE** `/api/cartoes/{id}`

### Block Card
**POST** `/api/cartoes/{id}/bloquear`
```json
{
    "motivo": "Solicitação do cliente"
}
```

## Insurance Management (Seguros)

### List Policies
**GET** `/api/seguros/apolices`
Query parameters:
- `cliente_id`: Filter by client
- `status`: Filter by status
- `per_page`: Items per page

### Create Policy
**POST** `/api/seguros/apolices`
```json
{
    "cliente_id": 1,
    "tipo_seguro_id": 1,
    "valor_segurado": 100000.00,
    "premio": 5000.00,
    "data_inicio": "2025-01-01",
    "data_fim": "2025-12-31"
}
```

### Get Policy
**GET** `/api/seguros/apolices/{id}`

### List Claims
**GET** `/api/seguros/sinistros`
Query parameters:
- `apolice_id`: Filter by policy
- `status`: Filter by status
- `per_page`: Items per page

### Create Claim
**POST** `/api/seguros/sinistros`
```json
{
    "apolice_id": 1,
    "descricao": "Acidente de trânsito",
    "valor_sinistro": 25000.00,
    "data_ocorrencia": "2025-01-15"
}
```

### Get Claim
**GET** `/api/seguros/sinistros/{id}`

## Currency Exchange (Câmbio)

### List Exchange Rates
**GET** `/api/taxas-cambio`
Query parameters:
- `moeda_origem`: Origin currency code
- `moeda_destino`: Destination currency code

### Get Quote
**GET** `/api/taxas-cambio/cotacao`
Query parameters:
- `moeda_origem`: Origin currency code (required)
- `moeda_destino`: Destination currency code (required)
- `valor`: Amount to convert (optional)

Example: `/api/taxas-cambio/cotacao?moeda_origem=USD&moeda_destino=AOA&valor=100`

### Create/Update Exchange Rate
**POST** `/api/taxas-cambio`
```json
{
    "moeda_origem_id": 1,
    "moeda_destino_id": 2,
    "taxa_compra": 825.50,
    "taxa_venda": 830.00,
    "ativa": true
}
```

### Exchange Operations History
**GET** `/api/operacoes-cambio`
Query parameters:
- `conta_id`: Filter by account
- `data_inicio`: Start date
- `data_fim`: End date
- `per_page`: Items per page

## Reports (Relatórios)

### Dashboard Metrics
**GET** `/api/relatorios/dashboard`
Returns general system metrics.

### Transaction Reports
**GET** `/api/relatorios/transacoes`
Query parameters:
- `data_inicio`: Start date
- `data_fim`: End date
- `tipo`: Transaction type
- `moeda`: Currency code
- `per_page`: Items per page

### Account Statement
**GET** `/api/relatorios/contas/{id}/extrato`
Query parameters:
- `data_inicio`: Start date (default: 1 month ago)
- `data_fim`: End date (default: today)
- `per_page`: Items per page

### Client Statement
**GET** `/api/relatorios/clientes/{id}/extrato`
Query parameters:
- `data_inicio`: Start date
- `data_fim`: End date

### Audit Report
**GET** `/api/relatorios/auditoria`
Query parameters:
- `data_inicio`: Start date
- `data_fim`: End date
- `acao`: Action to filter
- `per_page`: Items per page

## Error Handling

All endpoints return appropriate HTTP status codes:
- `200`: Success
- `201`: Created
- `204`: No Content (for deletes)
- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `422`: Validation Error

Error responses include details:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## Starting the Server

To start the Laravel development server:
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000/api/`

## API Documentation

Swagger documentation is available at:
`http://localhost:8000/api/documentation`

## Notes

1. All endpoints requiring authentication need the `Authorization: Bearer {token}` header
2. All POST/PUT requests should use `Content-Type: application/json`
3. Date formats should be in `YYYY-MM-DD` format
4. Currency amounts should be decimal numbers
5. The system supports multiple currencies and automatic exchange rate calculations
6. All CRUD operations are properly implemented and tested
7. Proper validation is implemented for all endpoints
8. The database configuration supports the default Laravel port (8000) on localhost
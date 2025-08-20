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

## Configuration Management (Configuração)

### List All Lookups
**GET** `/api/configuracoes/lookups`
Returns all types and status for dropdowns in a single call.

### Get All Types
**GET** `/api/configuracoes/tipos`
Returns all system types (client, account, card, insurance, transaction).

### Get All Status
**GET** `/api/configuracoes/status`
Returns all system statuses.

### Currency Management (Moedas)

#### List Currencies
**GET** `/api/moedas`
Query parameters:
- `ativa`: Filter by active currencies

#### Create Currency
**POST** `/api/moedas`
```json
{
    "codigo": "USD",
    "nome": "Dólar Americano",
    "simbolo": "$"
}
```

#### Get Currency
**GET** `/api/moedas/{id}`

#### Update Currency
**PUT** `/api/moedas/{id}`

#### Delete Currency
**DELETE** `/api/moedas/{id}`

### Branch Management (Agências)

#### List Branches
**GET** `/api/agencias`
Query parameters:
- `ativa`: Filter by active branches
- `codigo_agencia`: Filter by branch code

#### Create Branch
**POST** `/api/agencias`
```json
{
    "codigo_banco": "0042",
    "codigo_agencia": "0001",
    "nome": "Agência Central",
    "endereco": "Rua da Independência, 123",
    "telefone": ["+244 222 123 456"],
    "email": "central@banco.ao",
    "ativa": true
}
```

#### Get Branch
**GET** `/api/agencias/{id}`

#### Update Branch
**PUT** `/api/agencias/{id}`

#### Delete Branch
**DELETE** `/api/agencias/{id}`

## User Management (Gestão de Usuários)

### Profile Management (Perfis)

#### List Profiles
**GET** `/api/perfis`
Query parameters:
- `per_page`: Items per page

#### Create Profile
**POST** `/api/perfis`
```json
{
    "nome": "Gerente",
    "descricao": "Perfil para gerentes de agência",
    "permissoes": [1, 2, 3, 5, 8]
}
```

#### Get Profile
**GET** `/api/perfis/{id}`

#### Update Profile
**PUT** `/api/perfis/{id}`

#### Delete Profile
**DELETE** `/api/perfis/{id}`

#### Add Permissions to Profile
**POST** `/api/perfis/{id}/permissoes`
```json
{
    "permissoes": [10, 11, 12]
}
```

#### Remove Permission from Profile
**DELETE** `/api/perfis/{id}/permissoes/{permissao_id}`

### Permission Management (Permissões)

#### List Permissions
**GET** `/api/permissoes`
Query parameters:
- `grupo`: Filter by permission group

#### Create Permission
**POST** `/api/permissoes`
```json
{
    "nome": "Criar Cliente",
    "code": "cliente.create",
    "descricao": "Permite criar novos clientes",
    "grupo": "Clientes"
}
```

#### Get Permission
**GET** `/api/permissoes/{id}`

#### Update Permission
**PUT** `/api/permissoes/{id}`

#### Delete Permission
**DELETE** `/api/permissoes/{id}`

#### List Permission Groups
**GET** `/api/permissoes/grupos`

### Types and Status Endpoints

#### Client Types
**GET** `/api/tipos-cliente`
**POST** `/api/tipos-cliente`

#### Account Types
**GET** `/api/tipos-conta`

#### Card Types
**GET** `/api/tipos-cartao`

#### Insurance Types
**GET** `/api/tipos-seguro`

#### Transaction Types
**GET** `/api/tipos-transacao`

#### Client Status
**GET** `/api/status-cliente`
**POST** `/api/status-cliente`

#### Account Status
**GET** `/api/status-conta`

#### Card Status
**GET** `/api/status-cartao`

#### Payment Status
**GET** `/api/status-pagamento`

#### Claim Status
**GET** `/api/status-sinistro`

#### Transaction Status
**GET** `/api/status-transacao`

#### Policy Status
**GET** `/api/status-apolice`

## Payment Management (Pagamentos)

### List Payments
**GET** `/api/pagamentos`
Query parameters:
- `conta_id`: Filter by account
- `status`: Filter by status
- `parceiro`: Filter by partner
- `data_inicio`: Start date
- `data_fim`: End date
- `per_page`: Items per page

### Create Payment
**POST** `/api/pagamentos`
```json
{
    "conta_id": 1,
    "parceiro": "UNITEL",
    "referencia": "244912345678",
    "valor": 50.00,
    "moeda_id": 1,
    "data_pagamento": "2025-01-15 14:30:00",
    "status_pagamento_id": 1
}
```

### Get Payment
**GET** `/api/pagamentos/{id}`

### Update Payment
**PUT** `/api/pagamentos/{id}`

### Delete Payment
**DELETE** `/api/pagamentos/{id}`

### Process Payment
**POST** `/api/pagamentos/{id}/processar`

### Cancel Payment
**POST** `/api/pagamentos/{id}/cancelar`

## Audit Management (Auditoria)

### List Action Logs
**GET** `/api/logs`
Query parameters:
- `usuario_id`: Filter by user
- `acao`: Filter by action
- `tabela`: Filter by table
- `data_inicio`: Start date
- `data_fim`: End date
- `per_page`: Items per page

### Get Log Details
**GET** `/api/logs/{id}`

### Log Statistics
**GET** `/api/logs/estatisticas`
Query parameters:
- `periodo`: Period (hoje, semana, mes, ano)

### List Actions
**GET** `/api/logs/acoes`
Returns all recorded action types.

### List Monitored Tables
**GET** `/api/logs/tabelas`
Returns all monitored tables.

### User Logs
**GET** `/api/logs/usuario/{usuario_id}`
Query parameters:
- `per_page`: Items per page

### Clean Old Logs
**DELETE** `/api/logs/limpar`
```json
{
    "dias": 90
}
```

## Summary of All Available Endpoints

The system now includes **108 endpoints** organized in these categories:

### 🔐 **Authentication (4 endpoints)**
- Login, Logout, User Info, Change Password

### 👥 **Client Management (6 endpoints)**
- Full CRUD + lookups for clients

### 🏦 **Account Management (8 endpoints)**
- Full CRUD + deposit/withdraw/payment operations

### 💳 **Card Management (6 endpoints)**
- Full CRUD + block functionality

### 🔄 **Transaction Management (5 endpoints)**
- List, view, internal/external transfers, currency exchange

### 🛡️ **Insurance Management (6 endpoints)**
- Policies and claims management

### 💱 **Currency Exchange (4 endpoints)**
- Exchange rates and operations

### 📊 **Reports (5 endpoints)**
- Dashboard, statements, audit reports

### ⚙️ **Configuration (23 endpoints)**
- Currencies, branches, types, status, lookups

### 👤 **User Management (12 endpoints)**
- Profiles, permissions, user roles

### 💰 **Payment Management (7 endpoints)**
- Full payment lifecycle management

### 📋 **Audit Management (7 endpoints)**
- Action logs, statistics, cleanup

### 🔍 **Lookup Endpoints (15 endpoints)**
- All types and status for dropdowns

## Notes

1. All endpoints requiring authentication need the `Authorization: Bearer {token}` header
2. All POST/PUT requests should use `Content-Type: application/json`
3. Date formats should be in `YYYY-MM-DD` format
4. Currency amounts should be decimal numbers
5. The system supports multiple currencies and automatic exchange rate calculations
6. All CRUD operations are properly implemented and tested
7. Proper validation is implemented for all endpoints
8. The database configuration supports the default Laravel port (8000) on localhost
9. **NEW**: Complete configuration management for all system entities
10. **NEW**: Full user and permission management system
11. **NEW**: Comprehensive audit trail with statistics
12. **NEW**: Payment processing with status management
13. **NEW**: All lookup tables now have dedicated endpoints
# Sistema Bancário - Laravel

Sistema completo de gestão bancária desenvolvido em Laravel com interface responsiva e sistema de permissões avançado.

## 🚀 Funcionalidades

### Gestão de Clientes
- Cadastro completo de clientes
- Gestão de informações pessoais e bancárias
- Histórico de transações por cliente
- Relatórios detalhados

### Gestão de Contas
- Criação e gestão de contas bancárias
- Diferentes tipos de conta (Corrente, Poupança, etc.)
- Controle de saldos e movimentações
- Histórico de transações

### Gestão de Cartões
- Emissão e gestão de cartões
- Diferentes tipos de cartão
- Controle de status (Ativo, Bloqueado, etc.)
- Histórico de transações

### Transações
- Transferências internas e externas
- Depósitos e levantamentos
- Operações de câmbio
- Histórico completo de transações

### Seguros
- Gestão de apólices de seguro
- Controle de sinistros
- Diferentes tipos de seguro
- Relatórios de sinistros

### Relatórios
- Relatórios de clientes
- Relatórios de transações
- Relatórios de contas
- Relatórios de auditoria
- Exportação em PDF/Excel

### Administração
- Gestão de usuários
- Gestão de agências
- Gestão de perfis e permissões
- Configurações do sistema
- Logs de auditoria

### Sistema de Permissões
- Controle granular de acesso
- Perfis predefinidos (Administrador, Gerente, Atendente, Consultor)
- Permissões personalizadas por usuário
- Auditoria de ações

## 🛠️ Requisitos do Sistema

- PHP 8.1 ou superior
- Composer 2.0 ou superior
- MySQL 8.0 ou MariaDB 10.5 ou superior
- Node.js 16.0 ou superior (para assets)
- Git

## 📦 Instalação

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd sistema-bancario
```

### 2. Instale as dependências do PHP
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados
Edite o arquivo `.env` e configure as credenciais do banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_bancario
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Execute as migrações
```bash
php artisan migrate
```

### 6. Execute os seeders
```bash
php artisan db:seed
```

### 7. Configure o servidor web
```bash
# Para desenvolvimento
php artisan serve

# Para produção, configure o servidor web (Apache/Nginx) para apontar para a pasta public/
```

## 🔑 Credenciais de Acesso

Após executar os seeders, você terá acesso com as seguintes credenciais:

### Administrador
- **Email:** admin@banco.ao
- **Senha:** admin123
- **Perfil:** Administrador (acesso total)

### Perfis Disponíveis
1. **Administrador** - Acesso total ao sistema
2. **Gerente** - Acesso amplo a operações bancárias
3. **Atendente** - Acesso básico para atendimento
4. **Consultor** - Apenas visualização

## 🌐 Acesso ao Sistema

### Desenvolvimento
```
http://localhost:8000
```

### Produção
```
http://seu-dominio.com
```

## 📱 Interface Responsiva

O sistema é totalmente responsivo e funciona em:
- Desktop (Windows, macOS, Linux)
- Tablet (iPad, Android)
- Mobile (iPhone, Android)

## 🔐 Segurança

- Autenticação segura com Laravel
- Sistema de permissões granular
- Logs de auditoria completos
- Validação de dados em todas as operações
- Proteção CSRF
- Sanitização de inputs

## 📊 Estrutura do Banco de Dados

### Tabelas Principais
- `usuarios` - Usuários do sistema
- `perfis` - Perfis de usuário
- `permissoes` - Permissões do sistema
- `agencias` - Agências bancárias
- `clientes` - Clientes do banco
- `contas` - Contas bancárias
- `cartoes` - Cartões bancários
- `transacoes` - Transações bancárias
- `apolices` - Apólices de seguro
- `sinistros` - Sinistros de seguro

### Tabelas de Suporte
- `tipos_cliente` - Tipos de cliente
- `status_cliente` - Status de clientes
- `tipos_conta` - Tipos de conta
- `status_conta` - Status de contas
- `tipos_cartao` - Tipos de cartão
- `status_cartao` - Status de cartões
- `tipos_transacao` - Tipos de transação
- `status_transacao` - Status de transações
- `moedas` - Moedas disponíveis
- `taxas_cambio` - Taxas de câmbio

## 🚀 Comandos Úteis

### Desenvolvimento
```bash
# Iniciar servidor de desenvolvimento
php artisan serve

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Gerar chave da aplicação
php artisan key:generate

# Verificar rotas
php artisan route:list
```

### Banco de Dados
```bash
# Executar migrações
php artisan migrate

# Reverter migrações
php artisan migrate:rollback

# Executar seeders
php artisan db:seed

# Resetar banco e executar seeders
php artisan migrate:fresh --seed
```

### Produção
```bash
# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpar otimizações
php artisan optimize:clear
```

## 📁 Estrutura do Projeto

```
sistema-bancario/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores
│   │   ├── Middleware/      # Middlewares
│   │   └── Requests/        # Form Requests
│   ├── Models/              # Modelos Eloquent
│   └── Services/            # Serviços
├── database/
│   ├── migrations/          # Migrações
│   └── seeders/            # Seeders
├── resources/
│   └── views/              # Views Blade
├── routes/
│   ├── web.php             # Rotas web
│   └── api.php             # Rotas API
├── public/                 # Arquivos públicos
└── storage/                # Arquivos de storage
```

## 🔧 Configurações Adicionais

### Configurar Storage
```bash
php artisan storage:link
```

### Configurar Queue (opcional)
```bash
# Configurar driver de fila no .env
QUEUE_CONNECTION=database

# Criar tabela de jobs
php artisan queue:table
php artisan migrate

# Processar filas
php artisan queue:work
```

### Configurar Cache (opcional)
```bash
# Configurar driver de cache no .env
CACHE_DRIVER=redis

# Instalar Redis (Ubuntu/Debian)
sudo apt-get install redis-server
```

## 🐛 Solução de Problemas

### Erro de Permissões
```bash
# Dar permissões de escrita
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Erro de Composer
```bash
# Limpar cache do Composer
composer clear-cache
composer install --no-cache
```

### Erro de Banco de Dados
```bash
# Verificar conexão
php artisan tinker
DB::connection()->getPdo();

# Resetar banco
php artisan migrate:fresh --seed
```

## 📞 Suporte

Para suporte técnico ou dúvidas:
- Email: suporte@banco.ao
- Telefone: +244 123 456 789

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📈 Roadmap

- [ ] Integração com APIs bancárias
- [ ] Sistema de notificações push
- [ ] App mobile nativo
- [ ] Integração com WhatsApp Business
- [ ] Sistema de chat em tempo real
- [ ] Dashboard com gráficos avançados
- [ ] Relatórios automatizados
- [ ] Sistema de backup automático

---

**Desenvolvido com ❤️ usando Laravel**

# Integração Radar-Cobmais

Sistema de integração entre as plataformas Radar e Cobmais para gestão de cobranças.

## Funcionalidades

- Criação automática de cobranças no Cobmais a partir do Radar
- Recebimento de notificações de pagamento do Cobmais
- Atualização de status no Radar
- Rastreamento de referências cruzadas entre os sistemas
- Logging completo de operações

## Requisitos

- PHP 8.1 ou superior
- MySQL 8.0 ou superior
- Docker e Docker Compose
- Composer

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/radar-cobmais.git
cd radar-cobmais
```

2. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

3. Configure as variáveis de ambiente no arquivo `.env`:
```
# Configurações da API
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=radar_cobmais
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Cobmais API
COBMAIS_API_URL=https://api.cobmais.com.br
COBMAIS_SUBSCRIPTION_KEY=sua_chave

# Radar API
RADAR_API_URL=https://api.radar.com.br
RADAR_API_KEY=sua_chave

# Webhooks
WEBHOOK_SECRET=seu_segredo
```

4. Inicie os containers:
```bash
docker-compose up -d
```

5. Instale as dependências:
```bash
docker-compose exec app composer install
```

6. Execute a migração do banco de dados:
```bash
docker-compose exec db mysql -u root -p radar_cobmais < database/migrations/001_criar_tabela_referencias.sql
```

## Uso

### Criar Cobrança

Para criar uma cobrança no Cobmais a partir do Radar, envie uma requisição POST para:

```
POST /api/cobmais/cobrancas

{
    "id_radar": "123",
    "cpf_cnpj": "12345678901",
    "numero_contrato": "CONT123",
    "valor": 1000.00,
    "data_vencimento": "2024-03-20",
    "num_parcelas": 1,
    "desconto_principal": 10,
    "desconto_multa": 100,
    "desconto_juros": 100,
    "desconto_honorarios": 100
}
```

### Receber Notificação de Pagamento

O Cobmais enviará notificações de pagamento para:

```
POST /api/cobmais/webhook/pagamentos

{
    "id_acordo": 456,
    "data_pagamento": "2024-03-15",
    "valor_pagamento": 900.00
}
```

## Desenvolvimento

### Estrutura do Projeto

```
.
├── config/
│   ├── container.php
│   └── routes.php
├── database/
│   └── migrations/
├── docker/
│   ├── mysql/
│   └── nginx/
├── public/
│   └── index.php
├── src/
│   └── Application/
│       ├── Controller/
│       ├── Repository/
│       └── Services/
├── .env.example
├── composer.json
├── docker-compose.yml
└── Dockerfile
```

### Logs

Os logs da aplicação são enviados para o stdout e podem ser visualizados com:

```bash
docker-compose logs -f app
```

## Suporte

Para suporte, entre em contato com a equipe de desenvolvimento. 
# NestManager — Gestão de Alojamentos Locais

Aplicação de gestão de alojamentos locais (tipo Airbnb/Booking para proprietários), construída com **Laravel 11 + Filament v3**, containerizada com **Docker**, com sistema **RBAC** completo.

A aplicação será usada por **uma única empresa/pessoa** (sem multi-tenancy), mas com diferentes níveis de acesso para a equipa.

---

## Stack Tecnológico

| Camada | Tecnologia | Justificação |
|---|---|---|
| **Framework** | Laravel 11 | Ecossistema maduro, Eloquent ORM, migrations |
| **Admin Panel** | Filament v3 | UI rica out-of-the-box, formulários, tabelas, widgets |
| **PHP** | 8.3 | Última versão estável com suporte longo |
| **Base de Dados (Dev)** | SQLite | Simplicidade para desenvolvimento inicial |
| **Base de Dados (Prod)** | MySQL 8 / PostgreSQL 16 | Preparação para escalar — config separada por ambiente |
| **Containers** | Docker + Docker Compose | Ambiente isolado e reproduzível |
| **RBAC** | Spatie Permission + Filament Shield | Roles & Permissions integradas no Filament |
| **Media** | Spatie Media Library | Upload de fotos de propriedades |
| **Calendário** | saade/filament-fullcalendar | Visualização de reservas em calendário |

---

## Arquitectura da Base de Dados

### Diagrama ER (Entidades Principais)

```mermaid
erDiagram
    USERS ||--o{ ROLE_USER : has
    ROLES ||--o{ ROLE_USER : has
    ROLES ||--o{ ROLE_PERMISSION : has
    PERMISSIONS ||--o{ ROLE_PERMISSION : has

    USERS ||--o{ PROPERTIES : manages
    PROPERTIES ||--o{ ROOMS : contains
    PROPERTIES ||--o{ PROPERTY_AMENITIES : has
    AMENITIES ||--o{ PROPERTY_AMENITIES : has
    PROPERTIES ||--o{ PROPERTY_IMAGES : has
    PROPERTIES ||--o{ SEASONS : has

    GUESTS ||--o{ BOOKINGS : makes
    PROPERTIES ||--o{ BOOKINGS : receives
    ROOMS ||--o{ BOOKING_ROOMS : assigned_to
    BOOKINGS ||--o{ BOOKING_ROOMS : has
    BOOKINGS ||--o{ PAYMENTS : generates

    PROPERTIES {
        int id PK
        string name
        string slug
        text description
        string address
        string city
        string postal_code
        string country
        decimal latitude
        decimal longitude
        enum type "apartment|house|villa|room|studio"
        int max_guests
        int bedrooms
        int bathrooms
        decimal base_price_per_night
        decimal cleaning_fee
        time check_in_time
        time check_out_time
        text house_rules
        enum status "active|inactive|maintenance"
        timestamps
    }

    ROOMS {
        int id PK
        int property_id FK
        string name
        enum type "single|double|twin|suite|dormitory"
        int max_occupancy
        decimal price_override
        text description
        boolean is_active
        timestamps
    }

    GUESTS {
        int id PK
        string first_name
        string last_name
        string email
        string phone
        string phone_secondary
        date date_of_birth
        string nationality
        string id_document_type
        string id_document_number
        text address
        string city
        string country
        string postal_code
        text notes
        enum source "direct|airbnb|booking|other"
        timestamps
    }

    BOOKINGS {
        int id PK
        string reference_code
        int property_id FK
        int guest_id FK
        int created_by FK
        date check_in
        date check_out
        int num_guests
        int num_adults
        int num_children
        decimal price_per_night
        decimal cleaning_fee
        decimal extra_fees
        decimal discount
        decimal total_price
        enum status "pending|confirmed|checked_in|checked_out|cancelled|no_show"
        enum source "direct|airbnb|booking|other"
        text guest_notes
        text internal_notes
        timestamps
    }

    SEASONS {
        int id PK
        int property_id FK
        string name
        date start_date
        date end_date
        decimal price_per_night
        int min_nights
        timestamps
    }

    PAYMENTS {
        int id PK
        int booking_id FK
        decimal amount
        enum method "cash|bank_transfer|mbway|card|other"
        enum status "pending|completed|refunded|failed"
        date payment_date
        text notes
        timestamps
    }

    AMENITIES {
        int id PK
        string name
        string icon
        enum category "general|kitchen|bathroom|outdoor|safety|entertainment"
        timestamps
    }
```

---

## Sistema RBAC

### Roles e Permissões

| Role | Descrição | Permissões |
|---|---|---|
| **Super Admin** | Acesso total | Tudo — bypass de permissões |
| **Admin** | Gestor principal | CRUD completo em tudo, gestão de utilizadores |
| **Manager** | Gestor operacional | Gestão de propriedades, reservas, hóspedes, pagamentos |
| **Receptionist** | Receção / Check-in | Ver propriedades, criar/editar reservas, check-in/out, ver hóspedes |
| **Viewer** | Visualização apenas | Leitura em todas as áreas, sem edição |

Implementação via **Spatie Laravel Permission** + **Filament Shield** para auto-gerar policies e registar permissões por resource.

---

## Funcionalidades MVP (Fase 1)

### 1. Gestão de Propriedades
- CRUD de propriedades com fotos (Spatie Media Library)
- Lista de comodidades (amenities) com ícones
- Gestão de quartos por propriedade
- Status: ativa / inativa / manutenção

### 2. Calendário de Reservas
- Visualização em calendário (FullCalendar)
- Cores por status da reserva
- Drag & drop para alterar datas (opcional)
- Verificação automática de disponibilidade (sem overbooking)
- Filtro por propriedade

### 3. Gestão de Hóspedes
- CRUD de hóspedes com dados pessoais e documentos
- Histórico de reservas por hóspede
- Origem do hóspede (direto, Airbnb, Booking)
- Pesquisa rápida por nome, email, telefone

### 4. Sistema de Preços
- Preço base por noite (na propriedade)
- Épocas/temporadas com preços dinâmicos
- Taxa de limpeza
- Taxas extra e descontos por reserva
- Cálculo automático do total

### 5. Reservas
- Criação com seleção de propriedade, datas, hóspede
- Validação de disponibilidade em tempo real
- Código de referência automático (ex: `NM-2026-00042`)
- Estados: Pendente → Confirmada → Check-in → Check-out
- Registo de pagamentos parciais ou totais

### 6. Dashboard
- Widgets com KPIs: ocupação, receita mensal, reservas ativas
- Gráfico de receita (últimos 12 meses)
- Próximos check-ins / check-outs (hoje e amanhã)
- Propriedades com maior taxa de ocupação
- Reservas pendentes de confirmação

### 7. RBAC
- Gestão de utilizadores e roles no painel
- Filament Shield para policies automáticas
- Proteção de rotas e menus por permissão

---

## Estrutura Docker

```
docker/
├── nginx/
│   └── default.conf          # Config Nginx
├── php/
│   └── Dockerfile            # PHP 8.3-FPM + extensões
├── mysql/
│   └── my.cnf                # Config MySQL (produção)
docker-compose.yml             # Orquestração dos serviços
docker-compose.override.yml    # Overrides para dev (SQLite, volumes)
```

### Serviços Docker Compose

| Serviço | Imagem | Porta | Notas |
|---|---|---|---|
| **app** | PHP 8.3-FPM (custom) | — | Laravel + Filament |
| **nginx** | nginx:alpine | 8080:80 | Reverse proxy |
| **mysql** | mysql:8.0 | 3306 | Apenas para prod/staging |
| **redis** | redis:alpine | 6379 | Cache e queues |
| **mailpit** | mailpit | 8025 | Email testing (dev) |

> [!NOTE]
> Em desenvolvimento usaremos **SQLite** como base de dados (sem necessidade do container MySQL). A config de BD é separada por ambiente via `.env`.

---

## Estrutura de Ficheiros Laravel

```
app/
├── Enums/                     # BookingStatus, PropertyType, etc.
├── Filament/
│   ├── Resources/
│   │   ├── PropertyResource/  # CRUD Propriedades
│   │   ├── RoomResource/      # CRUD Quartos
│   │   ├── GuestResource/     # CRUD Hóspedes
│   │   ├── BookingResource/   # CRUD Reservas
│   │   ├── SeasonResource/    # CRUD Épocas
│   │   ├── PaymentResource/   # CRUD Pagamentos
│   │   ├── AmenityResource/   # CRUD Comodidades
│   │   └── UserResource/      # Gestão Utilizadores
│   ├── Widgets/
│   │   ├── StatsOverview.php
│   │   ├── RevenueChart.php
│   │   ├── UpcomingCheckIns.php
│   │   └── BookingCalendar.php
│   └── Pages/
│       └── Dashboard.php
├── Models/
│   ├── Property.php
│   ├── Room.php
│   ├── Guest.php
│   ├── Booking.php
│   ├── Season.php
│   ├── Payment.php
│   └── Amenity.php
├── Policies/                  # Auto-geradas pelo Shield
├── Services/
│   ├── AvailabilityService.php
│   ├── PricingService.php
│   └── BookingService.php
└── Observers/
    └── BookingObserver.php     # Auto-gerar reference_code, etc.

database/
├── migrations/
│   ├── create_properties_table
│   ├── create_rooms_table
│   ├── create_guests_table
│   ├── create_bookings_table
│   ├── create_booking_rooms_table
│   ├── create_seasons_table
│   ├── create_payments_table
│   ├── create_amenities_table
│   └── create_property_amenities_table
└── seeders/
    ├── RoleSeeder.php
    ├── AmenitySeeder.php
    └── DemoDataSeeder.php
```

---

## Proposed Changes (Ordem de Implementação)

### Fase 0 — Infraestrutura Docker + Laravel + Filament

#### [NEW] `docker-compose.yml`
Orquestração com serviços: app (PHP-FPM), nginx, redis, mailpit.

#### [NEW] `docker/php/Dockerfile`
PHP 8.3-FPM com extensões: pdo_sqlite, pdo_mysql, gd, zip, intl, redis.

#### [NEW] `docker/nginx/default.conf`
Config Nginx para servir Laravel.

#### [NEW] `.env.example`
Variáveis de ambiente com defaults para SQLite em dev.

Após os containers, instalar Laravel 11 + Filament v3 + packages:
```bash
composer create-project laravel/laravel .
composer require filament/filament:"^3.0"
composer require spatie/laravel-permission
composer require filament/spatie-laravel-media-library-plugin:"^3.0"
composer require bezhansalleh/filament-shield:"^3.0"
composer require saade/filament-fullcalendar:"^3.0"
```

---

### Fase 1 — Modelos, Migrations, Enums

#### [NEW] Enums: `BookingStatus`, `PropertyType`, `PropertyStatus`, `RoomType`, `PaymentMethod`, `PaymentStatus`, `GuestSource`, `AmenityCategory`

#### [NEW] Migrations para todas as tabelas do diagrama ER

#### [NEW] Models com relationships, casts, e scopes

#### [NEW] Seeders: Roles, Amenities pré-definidas, dados demo

---

### Fase 2 — Filament Resources (CRUD)

#### [NEW] `PropertyResource` — CRUD com tabs (Info, Quartos, Fotos, Comodidades, Épocas)
#### [NEW] `RoomResource` — Nested dentro de Property
#### [NEW] `GuestResource` — com pesquisa e filtros
#### [NEW] `BookingResource` — com wizard de criação, validação de disponibilidade
#### [NEW] `SeasonResource` — gestão de épocas por propriedade
#### [NEW] `PaymentResource` — registo de pagamentos
#### [NEW] `AmenityResource` — lista global de comodidades
#### [NEW] `UserResource` — gestão de utilizadores e roles

---

### Fase 3 — Lógica de Negócio

#### [NEW] `AvailabilityService` — verificação de conflitos de datas
#### [NEW] `PricingService` — cálculo de preço com base em épocas
#### [NEW] `BookingService` — orquestração de criação de reserva
#### [NEW] `BookingObserver` — auto-gerar reference_code

---

### Fase 4 — Dashboard e Calendário

#### [NEW] Dashboard widgets: KPIs, gráficos de receita, próximos check-ins
#### [NEW] Página de calendário com FullCalendar

---

### Fase 5 — RBAC com Shield

#### [NEW] Configuração de roles e permissions
#### [NEW] Policies auto-geradas para cada resource
#### [NEW] Seeder com roles e permissões padrão

---

## Verificação

### Testes Automatizados
```bash
php artisan test
```
- Testes unitários para `AvailabilityService` e `PricingService`
- Testes de feature para criação de reservas (sem overbooking)
- Testes de policies (RBAC)

### Verificação Manual
- Aceder ao painel Filament e testar cada CRUD
- Criar uma reserva completa (propriedade → hóspede → reserva → pagamento)
- Verificar que o calendário mostra as reservas
- Testar diferentes roles (admin vs receptionist)
- Validar que SQLite funciona em dev e MySQL em prod

---

## Fases Futuras (Pós-MVP)

| Fase | Funcionalidade |
|---|---|
| Fase 6 | Faturação e exportação PDF |
| Fase 7 | Notificações por email (confirmação, lembrete check-in) |
| Fase 8 | API REST para integrações |
| Fase 9 | Integração com canais (iCal Airbnb/Booking) |
| Fase 10 | Portal do hóspede (self-service) |
| Fase 11 | Relatórios avançados e exportação |
| Fase 12 | App mobile (PWA ou Flutter) |

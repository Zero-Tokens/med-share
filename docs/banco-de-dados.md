# Banco de dados

Modelagem do med-share. Este documento acompanha as migrations em `database/migrations/` e deve ser atualizado junto com elas.

Cobre a modelagem inicial da issue #2 em três partes, seguindo a [seção 11 do README](../README.md#11-modelo-inicial-de-dados): **parte 1**, território, unidades, vínculos de acesso e catálogo de medicamentos; **parte 2**, solicitações, ofertas, reservas e transferências; **parte 3**, importação dos dados oficiais, qualidade dos dados e apoio operacional (idempotência, outbox, auditoria).

## Convenções

- Banco de desenvolvimento e produção: PostgreSQL 17. Testes: SQLite em memória. As migrations não usam recursos exclusivos de um deles (enums são colunas `string`, chaves estrangeiras com `constrained()`).
- Códigos de identificação externos (`ibge_code`, `uf_code`, `cnes`, `external_code`) são **texto**, para preservar zeros à esquerda.
- Quantidades são `decimal(14,3)` e chegam ao PHP como **string** (cast `decimal:3`); contas com `bcmath`, nunca `float`. `medications.quantity_precision` (0 a 3) diz quantas casas cada unidade de fornecimento admite; a validação dos comandos aplica esse limite.
- Situações e perfis são `string` no banco e enums de PHP em `app/Enums/`.
- Instantes (`*_at` com hora) em UTC. `starts_at` e `ends_at` dos vínculos são datas civis, sem fuso.
- Remoção: um usuário removido leva seus vínculos junto (`cascade`) e deixa de ser referenciado como revisor ou aprovador (`set null`). Municípios e unidades com registros dependentes não podem ser removidos (`restrict`).

## Diagrama

```mermaid
erDiagram
    users ||--o{ municipality_memberships : "tem"
    users ||--o{ facility_memberships : "tem"
    users |o--o{ municipality_memberships : "aprova"
    users |o--o{ facility_memberships : "aprova"
    users |o--o{ medication_mappings : "revisa"
    municipalities ||--o{ facilities : "contém"
    municipalities ||--o{ municipality_memberships : "vincula"
    facilities ||--o{ facility_memberships : "vincula"
    medications ||--o{ medication_mappings : "identificado por"

    users {
        bigint id PK
        string name
        string email UK
        boolean is_platform_admin
    }
    municipalities {
        bigint id PK
        string ibge_code UK "7 dígitos"
        string uf_code "2 dígitos"
        string uf "sigla"
        string name
        decimal latitude
        decimal longitude
    }
    facilities {
        bigint id PK
        bigint municipality_id FK
        string cnes UK "7 dígitos"
        string name
        string address
        string postal_code
        decimal latitude
        decimal longitude
        boolean is_active
    }
    municipality_memberships {
        bigint id PK
        bigint user_id FK
        bigint municipality_id FK
        string role
        string status
        bigint approved_by FK
        timestamp approved_at
        date starts_at
        date ends_at
    }
    facility_memberships {
        bigint id PK
        bigint user_id FK
        bigint facility_id FK
        string role
        string status
        bigint approved_by FK
        timestamp approved_at
        date starts_at
        date ends_at
    }
    medications {
        bigint id PK
        string name
        string active_ingredient
        string strength
        string pharmaceutical_form
        string presentation
        string supply_unit
        tinyint quantity_precision
        boolean is_active
    }
    medication_mappings {
        bigint id PK
        bigint medication_id FK
        string source
        string external_code
        string external_description
        string status
        bigint reviewed_by FK
        timestamp reviewed_at
    }
```

## Dicionário de dados

Todas as tabelas têm `id` (bigint autoincremento), `created_at` e `updated_at`.

### `users`

Contas individuais. Vem do starter kit; a modelagem acrescentou uma coluna.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `is_platform_admin` | boolean, padrão `false` | Administra a plataforma (adesões, importações, inconsistências). Não é vínculo com município ou unidade. |

### `municipalities`

Município e códigos territoriais de referência.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `ibge_code` | string(7), único | Código IBGE do município, com 7 dígitos |
| `uf_code` | string(2) | Código IBGE da UF (ex.: `25`) |
| `uf` | string(2) | Sigla da UF (ex.: `PB`) |
| `name` | string | Nome do município |
| `latitude`, `longitude` | decimal(9,6), nulos | Coordenadas da sede, quando conhecidas |

Índices: `uf`.

### `facilities`

Unidade de saúde (estabelecimento com CNES).

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `municipality_id` | FK → `municipalities`, restrict | Município da unidade |
| `cnes` | string(7), único | Código CNES, com 7 dígitos |
| `name` | string | Nome da unidade |
| `address` | string, nulo | Endereço em texto |
| `postal_code` | string(8), nulo | CEP, só dígitos |
| `latitude`, `longitude` | decimal(9,6), nulos | Coordenadas. Ausentes ou suspeitas, a unidade não pode aparecer como "mais próxima" sem validação |
| `is_active` | boolean, padrão `true` | Unidade habilitada na plataforma |

Índices: (`municipality_id`, `is_active`).

### `municipality_memberships` e `facility_memberships`

Vínculo de um usuário com um município ou com uma unidade. Mesma estrutura; o que muda é a coluna de escopo (`municipality_id` ou `facility_id`). Uma pessoa pode ter vínculos em vários municípios e unidades, mas só um por escopo.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `user_id` | FK → `users`, cascade | Usuário vinculado |
| `municipality_id` / `facility_id` | FK, restrict | Escopo do vínculo |
| `role` | string(30) | Perfil, enum `MembershipRole`: `municipal_manager`, `facility_responsible`, `transfer_approver`, `logistics_operator`, `auditor` |
| `status` | string(20) | Enum `MembershipStatus`: `pending`, `active`, `suspended`, `revoked`. Só `active` concede acesso |
| `approved_by` | FK → `users`, set null | Quem aprovou o vínculo |
| `approved_at` | timestamp, nulo | Quando foi aprovado |
| `starts_at`, `ends_at` | date, nulos | Validade do vínculo, quando houver |

Restrições: único (`user_id`, `municipality_id`) e único (`user_id`, `facility_id`). Índices: (`municipality_id`, `status`) e (`facility_id`, `status`).

Os perfis previstos para município são `municipal_manager`, `transfer_approver` e `auditor`; para unidade, `facility_responsible`, `transfer_approver`, `logistics_operator` e `auditor`. O banco não impõe essa separação; ela fica na validação das telas de cadastro.

### `medications`

Identidade interna do medicamento. A compatibilidade entre dois itens considera princípio ativo, concentração, forma farmacêutica, apresentação e unidade de fornecimento, e não o nome.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `name` | string | Descrição de exibição (ex.: `Amoxicilina 500 mg cápsula`) |
| `active_ingredient` | string, nulo | Princípio ativo ou composição |
| `strength` | string, nulo | Concentração (ex.: `500 mg`) |
| `pharmaceutical_form` | string, nulo | Forma farmacêutica (ex.: `cápsula`) |
| `presentation` | string, nulo | Apresentação (ex.: `blister com 10`) |
| `supply_unit` | string | Unidade de fornecimento em que as quantidades são contadas (ex.: `comprimido`, `frasco`) |
| `quantity_precision` | tinyint, padrão `0` | Casas decimais permitidas nas quantidades dessa unidade |
| `is_active` | boolean, padrão `true` | Item disponível para novas solicitações e ofertas |

Índices: `name`, `active_ingredient`. **Não há unicidade por nome**: a identidade vem dos códigos externos mapeados.

### `medication_mappings`

Códigos externos (CATMAT, OBM) ligados a um medicamento interno, com situação de revisão.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `medication_id` | FK → `medications`, cascade | Medicamento interno |
| `source` | string(20) | Enum `ExternalCodeSource`: `catmat`, `obm` |
| `external_code` | string(30) | Código na fonte, como texto |
| `external_description` | string, nulo | Descrição do item na fonte, preservada para revisão |
| `status` | string(20) | Enum `MedicationMappingStatus`: `pending`, `confirmed`, `rejected`. Só `confirmed` vale para compatibilidade |
| `reviewed_by` | FK → `users`, set null | Quem revisou |
| `reviewed_at` | timestamp, nulo | Quando foi revisado |

Restrições: único (`source`, `external_code`), ou seja, um código externo aponta para um só medicamento interno. Índices: (`medication_id`, `status`).

## Operação: solicitações, ofertas, reservas e transferências

### Diagrama

```mermaid
erDiagram
    facilities ||--o{ medication_requests : "destino"
    facilities ||--o{ transfer_offers : "origem"
    facilities ||--o{ transfers : "origem"
    facilities ||--o{ transfers : "destino"
    medications ||--o{ medication_request_items : "pedido"
    medications ||--o{ transfer_offers : "ofertado"
    medication_requests ||--o{ medication_request_items : "contém"
    medication_requests |o--o{ transfers : "atendida por"
    transfer_offers ||--o{ transfer_offer_lots : "lotes"
    transfers ||--o{ reservations : "compromete"
    medication_request_items ||--o{ reservations : "atendido por"
    transfer_offer_lots ||--o{ reservations : "reservado"
    reservations ||--o| transfer_items : "executada por"
    transfers ||--o{ transfer_items : "itens"
    transfers ||--o{ transfer_approvals : "aprovações"
    transfers ||--o{ transfer_events : "eventos"
    transfers ||--o{ transfer_discrepancies : "divergências"
    transfer_items |o--o{ transfer_discrepancies : "sobre"

    medication_requests {
        bigint id PK
        bigint facility_id FK
        bigint requested_by FK
        string status
        timestamp opened_at
        timestamp closed_at
    }
    medication_request_items {
        bigint id PK
        bigint medication_request_id FK
        bigint medication_id FK
        decimal quantity_requested
        decimal quantity_committed
        decimal quantity_shipped
        decimal quantity_received
        string urgency
        date needed_by
    }
    transfer_offers {
        bigint id PK
        bigint facility_id FK
        bigint medication_id FK
        string status
        bigint confirmed_by FK
        timestamp confirmed_at
        timestamp expires_at
    }
    transfer_offer_lots {
        bigint id PK
        bigint transfer_offer_id FK
        string lot_number
        date expires_on
        decimal quantity_released
        decimal quantity_reserved
        decimal quantity_shipped
        boolean is_blocked
    }
    transfers {
        bigint id PK
        bigint origin_facility_id FK
        bigint destination_facility_id FK
        bigint medication_request_id FK
        string status
        bigint requested_by FK
    }
    reservations {
        bigint id PK
        bigint transfer_id FK
        bigint medication_request_item_id FK
        bigint transfer_offer_lot_id FK
        string status
        decimal quantity
        decimal quantity_collected
        timestamp expires_at
    }
    transfer_items {
        bigint id PK
        bigint transfer_id FK
        bigint reservation_id FK "único"
        bigint medication_id FK
        bigint transfer_offer_lot_id FK
        decimal quantity_planned
        decimal quantity_collected
        decimal quantity_received
        decimal quantity_accepted
    }
    transfer_approvals {
        bigint id PK
        bigint transfer_id FK
        bigint approved_by FK
        string role
        string decision
        string items_version
        timestamp decided_at
    }
    transfer_events {
        bigint id PK
        bigint transfer_id FK
        string type
        bigint actor_id FK
        timestamp occurred_at
        json payload
    }
    transfer_discrepancies {
        bigint id PK
        bigint transfer_id FK
        bigint transfer_item_id FK
        string type
        string status
        decimal quantity_expected
        decimal quantity_found
    }
```

### `medication_requests`

Necessidade declarada por uma unidade de destino.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `facility_id` | FK → `facilities`, restrict | Unidade de destino |
| `requested_by` | FK → `users`, set null | Quem abriu |
| `status` | string(30) | Enum `RequestStatus`: `draft`, `open`, `partially_fulfilled`, `fulfilled`, `cancelled`, `closed_unfulfilled`. `fulfilled` só pelas quantidades recebidas e aceitas |
| `notes` | text, nulo | Observações |
| `opened_at`, `closed_at` | timestamp, nulos | Abertura e encerramento |

Índices: (`facility_id`, `status`).

### `medication_request_items`

Um item por medicamento dentro da solicitação. As quantidades ficam separadas para evitar atendimento duplicado.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `medication_request_id` | FK, cascade | Solicitação |
| `medication_id` | FK → `medications`, restrict | Medicamento, com apresentação e unidade de fornecimento |
| `quantity_requested` | decimal(14,3) | Necessária |
| `quantity_committed` | decimal(14,3) | Reservada em transferências ativas |
| `quantity_shipped` | decimal(14,3) | Já expedida pelas origens |
| `quantity_received` | decimal(14,3) | Recebida e aceita no destino |
| `urgency` | string(20) | Enum `RequestUrgency`: `routine`, `urgent`, `critical` |
| `needed_by` | date, nulo | Prazo |

Restrições: único (`medication_request_id`, `medication_id`). Índices: (`medication_id`, `urgency`, `needed_by`).

### `transfer_offers`

Liberação explícita de uma unidade de origem para um medicamento. Não é o estoque da fonte pública.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `facility_id` | FK → `facilities`, restrict | Unidade de origem |
| `medication_id` | FK → `medications`, restrict | Medicamento ofertado |
| `status` | string(20) | Enum `OfferStatus`: `draft`, `active`, `paused`, `exhausted`, `expired`, `closed`. Só `active` admite reservas |
| `confirmed_by`, `confirmed_at` | FK set null, timestamp | Responsável e momento da confirmação |
| `expires_at` | timestamp, nulo | Validade da confirmação da unidade |
| `notes` | text, nulo | Observações |

Índices: (`medication_id`, `status`, `expires_at`), (`facility_id`, `status`).

### `transfer_offer_lots`

Lotes liberados dentro de uma oferta, com o controle de saldo.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `transfer_offer_id` | FK, cascade | Oferta |
| `lot_number` | string(50) | Número do lote |
| `expires_on` | date | Validade do lote (data civil) |
| `quantity_released` | decimal(14,3) | Liberada pela unidade |
| `quantity_reserved` | decimal(14,3) | Comprometida em reservas ativas |
| `quantity_shipped` | decimal(14,3) | Já coletada; consome a oferta e nunca volta ao saldo |
| `is_blocked` | boolean | Lote bloqueado para novas reservas |

Restrições: único (`transfer_offer_id`, `lot_number`). Índices: `expires_on`.

**Saldo ofertável** = `quantity_released − quantity_reserved − quantity_shipped`, calculado por `TransferOfferLot::availableQuantity()` com `bcmath`; não é gravado. A liberada não pode cair abaixo dos compromissos, e a soma das reservas concorrentes não pode passar do saldo: regras das ações, em transação com bloqueio de linha.

### `transfers`

Proposta de transferência entre uma origem e um destino. Atendimento por várias origens gera transferências distintas.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `origin_facility_id`, `destination_facility_id` | FK → `facilities`, restrict | Origem e destino (devem ser diferentes; validado nas ações) |
| `medication_request_id` | FK, restrict, nulo | Solicitação atendida |
| `status` | string(30) | Enum `TransferStatus`: `pending_approval`, `approved`, `reserved`, `ready_for_pickup`, `partially_collected`, `collected`, `in_transit`, `received_pending_review`, `disputed`, `completed`, `cancelled` |
| `requested_by` | FK → `users`, set null | Quem propôs |
| `approved_at` … `cancelled_at` | timestamps, nulos | Marcos do fluxo |
| `notes` | text, nulo | Observações |

Índices: (`origin_facility_id`, `status`), (`destination_facility_id`, `status`), `medication_request_id`.

### `reservations`

Compromisso de quantidade entre um item solicitado, um lote ofertado e uma transferência. Protege o saldo da oferta contra concorrência.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `transfer_id` | FK → `transfers`, restrict | Transferência; não se apaga transferência com reserva |
| `medication_request_item_id` | FK, restrict | Item atendido |
| `transfer_offer_lot_id` | FK, restrict | Lote comprometido |
| `status` | string(30) | Enum `ReservationStatus`: `active`, `partially_collected`, `collected`, `released`, `expired`, `cancelled`. Reserva coletada não expira nem é liberada |
| `quantity` | decimal(14,3) | Reservada |
| `quantity_collected` | decimal(14,3) | Já coletada desta reserva |
| `expires_at` | timestamp, nulo | Vencimento da reserva |
| `reserved_by` | FK → `users`, set null | Quem reservou |

Índices: (`transfer_offer_lot_id`, `status`), (`medication_request_item_id`, `status`), `transfer_id`, (`status`, `expires_at`).

### `transfer_items`

Execução física de uma reserva: previsto, coletado, recebido e aceito. Um para um com a reserva.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `transfer_id` | FK, cascade | Transferência |
| `reservation_id` | FK, restrict, **único** | Reserva executada |
| `medication_id` | FK, restrict | Medicamento |
| `transfer_offer_lot_id` | FK, restrict | Lote |
| `quantity_planned` | decimal(14,3) | Previsto (= reserva) |
| `quantity_collected` | decimal(14,3) | Conferido na coleta |
| `quantity_received` | decimal(14,3) | Conferido no recebimento |
| `quantity_accepted` | decimal(14,3) | Aceito após conciliação |

Índices: `transfer_id`, `medication_id`, `transfer_offer_lot_id`.

### `transfer_approvals`

Decisões registradas sobre a transferência.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `transfer_id` | FK, cascade | Transferência |
| `approved_by` | FK → `users`, **restrict** | Quem decidiu; usuário com aprovações não pode ser removido |
| `role` | string(30) | Enum `MembershipRole` em que a pessoa decidiu |
| `decision` | string(20) | Enum `ApprovalDecision`: `approved`, `rejected` |
| `items_version` | string(64) | Hash dos itens, lotes e quantidades aprovados; alteração material invalida a aprovação |
| `notes` | text, nulo | Justificativa |
| `decided_at` | timestamp | Momento da decisão |

Índices: (`transfer_id`, `decision`).

### `transfer_events`

Trilha operacional. A sequência de eventos reconstrói o histórico da transferência.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `transfer_id` | FK, cascade | Transferência |
| `type` | string(40) | Enum `TransferEventType`: `created`, `approved`, `rejected`, `reserved`, `ready_for_pickup`, `collected`, `in_transit`, `received`, `disputed`, `reconciled`, `completed`, `cancelled`, `note` |
| `actor_id` | FK → `users`, set null | Responsável |
| `occurred_at` | timestamp | Quando aconteceu |
| `payload` | json, nulo | Quantidades, lotes, referências de evidências |
| `notes` | text, nulo | Observações |

Índices: (`transfer_id`, `occurred_at`).

### `transfer_discrepancies`

Divergências apontadas na coleta ou no recebimento e sua conciliação.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `transfer_id` | FK, cascade | Transferência |
| `transfer_item_id` | FK, cascade, nulo | Item afetado, quando houver |
| `type` | string(30) | Enum `DiscrepancyType`: `quantity`, `lot`, `expiry`, `damage`, `other` |
| `status` | string(20) | Enum `DiscrepancyStatus`: `open`, `reconciled`, `dismissed` |
| `quantity_expected`, `quantity_found` | decimal(14,3), nulos | Esperado e encontrado |
| `description` | text | Relato |
| `reported_by`, `reconciled_by` | FK → `users`, set null | Quem apontou e quem conciliou |
| `reconciled_at`, `resolution` | timestamp, text, nulos | Conciliação |

Índices: (`transfer_id`, `status`), `transfer_item_id`.

## Importação, qualidade dos dados e apoio operacional

### Diagrama

```mermaid
erDiagram
    data_sources ||--o{ data_source_versions : "versões observadas"
    data_sources ||--o{ integration_runs : "execuções"
    data_source_versions |o--o{ integration_runs : "dispara"
    integration_runs ||--o{ integration_partitions : "recortes"
    integration_partitions ||--o{ integration_payloads : "páginas"
    data_sources ||--o{ external_stock_positions : "histórico"
    integration_payloads |o--o{ external_stock_positions : "origem"
    facilities |o--o{ external_stock_positions : "resolvida"
    medications |o--o{ external_stock_positions : "resolvido"
    external_stock_positions ||--o| external_stock_current : "projeção"
    facilities ||--o{ external_stock_current : "unidade"
    medications ||--o{ external_stock_current : "medicamento"
    integration_runs |o--o{ data_quality_issues : "gera"
    external_stock_positions |o--o{ data_quality_issues : "sobre"
    users |o--o{ audit_logs : "ator"

    data_sources {
        bigint id PK
        string slug UK
        string name
        string base_url
        string resource_url
        boolean is_enabled
        json settings
    }
    data_source_versions {
        bigint id PK
        bigint data_source_id FK
        string version_key
        timestamp observed_at
        json metadata
    }
    integration_runs {
        bigint id PK
        bigint data_source_id FK
        bigint data_source_version_id FK
        string kind
        string status
        json scope
        timestamp started_at
        timestamp finished_at
        int pages_processed
        int records_valid
        int records_rejected
    }
    integration_partitions {
        bigint id PK
        bigint integration_run_id FK
        string partition_key
        json filters
        string status
        int next_offset "checkpoint"
        int attempts
    }
    integration_payloads {
        bigint id PK
        bigint integration_partition_id FK
        int page_offset
        string content_hash
        string storage_path
        int record_count
        timestamp fetched_at
    }
    external_stock_positions {
        bigint id PK
        bigint data_source_id FK
        bigint integration_payload_id FK
        bigint facility_id FK "nulo se CNES desconhecido"
        bigint medication_id FK "nulo se sem mapeamento"
        string cnes
        string external_medication_code
        string lot_number
        date expires_on
        decimal quantity
        date position_date
        timestamp imported_at
        string record_hash
        json raw
    }
    external_stock_current {
        bigint id PK
        bigint external_stock_position_id FK "único"
        bigint data_source_id FK
        bigint facility_id FK
        bigint medication_id FK
        decimal quantity
        date position_date
        timestamp imported_at
        string quality_status
    }
    data_quality_issues {
        bigint id PK
        bigint integration_run_id FK
        bigint external_stock_position_id FK
        string type
        string severity
        string status
        text message
        json details
    }
    idempotency_keys {
        bigint id PK
        string actor_key
        string operation
        string idempotency_key
        string content_hash
        json response_body
    }
    outbox_events {
        bigint id PK
        string event_type
        string aggregate_type
        bigint aggregate_id
        json payload
        string status
        timestamp available_at
    }
    audit_logs {
        bigint id PK
        bigint user_id FK
        string action
        string auditable_type
        bigint auditable_id
        json changes
        timestamp occurred_at
    }
```

### `data_sources`

Fonte pública habilitada. O seeder `DataSourceSeeder` cria a fonte `bnafar_horus` (API de Dados Abertos do Ministério da Saúde).

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `slug` | string(40), único | Identificador estável (ex.: `bnafar_horus`) |
| `name` | string | Nome de exibição |
| `base_url` | string | Raiz da API |
| `resource_url` | string, nulo | Recurso de metadados monitorado para disparar importações (issue #10); precisa de validação (issue #9) |
| `is_enabled` | boolean | Fonte ativa |
| `settings` | json, nulo | Endpoint, tamanho de página e outros parâmetros do contrato |

### `data_source_versions`

Versões observadas nos metadados da fonte: é o "controle de versões monitoradas" que o worker usa para decidir se há dado novo.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `data_source_id` | FK, cascade | Fonte |
| `version_key` | string(120) | Chave da versão (data de modificação, hash do recurso) |
| `observed_at` | timestamp | Quando foi vista |
| `metadata` | json, nulo | Metadados originais |

Restrições: único (`data_source_id`, `version_key`). Índices: `observed_at`.

### `integration_runs`

Execução de importação com seu recorte e resultado.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `data_source_id` | FK, restrict | Fonte |
| `data_source_version_id` | FK, set null, nulo | Versão que disparou a execução |
| `kind` | string(20) | Enum `IntegrationRunKind`: `initial`, `incremental`, `reprocess` |
| `status` | string(30) | Enum `IntegrationRunStatus`: `pending`, `running`, `completed`, `completed_with_errors`, `failed`, `cancelled`. Execução incompleta não zera saldos nem substitui posição confiável |
| `scope` | json, nulo | Filtros da execução (UF, município, datas) |
| `requested_by` | FK → `users`, set null | Quem pediu, quando manual |
| `started_at`, `finished_at` | timestamps, nulos | Duração |
| `pages_processed`, `records_valid`, `records_rejected` | int | Totais consolidados |
| `error_message` | text, nulo | Falha geral |

Índices: (`data_source_id`, `status`), (`status`, `started_at`).

### `integration_partitions`

Recorte de uma execução (UF, município, data) com checkpoint próprio. Uma falha permite retomar a partição sem reiniciar o país.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `integration_run_id` | FK, cascade | Execução |
| `partition_key` | string(120) | Ex.: `uf=25;municipio=2507507;data=2026-11-01` |
| `filters` | json, nulo | Parâmetros enviados à fonte |
| `status` | string(20) | Enum `IntegrationPartitionStatus`: `pending`, `running`, `completed`, `failed` |
| `next_offset` | int | Próxima página a buscar; só avança depois que a página foi persistida |
| `pages_processed`, `records_valid`, `records_rejected` | int | Totais da partição |
| `attempts` | smallint | Tentativas |
| `last_error` | text, nulo | Última falha |
| `started_at`, `finished_at` | timestamps, nulos | Duração |

Restrições: único (`integration_run_id`, `partition_key`). Índices: (`status`, `integration_run_id`).

### `integration_payloads`

Referência à resposta original de cada página, guardada no armazenamento privado.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `integration_partition_id` | FK, cascade | Partição |
| `page_offset` | int | Página (o `offset` da fonte é número de página) |
| `content_hash` | string(64) | SHA-256 do corpo |
| `storage_path` | string | Caminho do arquivo |
| `record_count` | int | Registros na página |
| `http_status` | smallint, nulo | Resposta HTTP |
| `fetched_at` | timestamp | Momento da coleta |

Restrições: único (`integration_partition_id`, `page_offset`, `content_hash`): a mesma página com conteúdo diferente é guardada de novo; igual, não. Índices: `content_hash`.

### `external_stock_positions`

Histórico das posições importadas, com as dimensões originais. Nunca é sobrescrito por uma nova importação.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `data_source_id` | FK, restrict | Fonte |
| `integration_payload_id` | FK, set null, nulo | Página de origem |
| `facility_id` | FK, restrict, nulo | Unidade resolvida pelo CNES; nulo quando desconhecido |
| `medication_id` | FK, restrict, nulo | Medicamento resolvido por mapeamento confirmado; nulo quando não mapeado |
| `cnes` | string(7) | CNES como veio |
| `external_medication_code` | string(30) | Código na fonte (CATMAT) |
| `external_medication_description` | string, nulo | Descrição na fonte |
| `lot_number`, `expires_on` | string(50), date, nulos | Lote e validade |
| `quantity` | decimal(14,3) | Quantidade informada |
| `program_code`, `origin_system`, `product_type` | strings, nulos | Programa, sistema de origem e tipo de produto |
| `position_date` | date | Data da posição, segundo a fonte |
| `imported_at` | timestamp | Instante em que importamos; nunca confundir com a anterior |
| `record_hash` | string(64) | Hash da chave composta: fonte, CNES, produto, lote, validade, programa, sistema de origem, data |
| `raw` | json, nulo | Campos originais do registro, para revisar a normalização |

Restrições: único (`data_source_id`, `record_hash`). Registro com a mesma chave e valores diferentes gera `data_quality_issues` (`conflicting_values`), não sobrescreve. Índices: (`facility_id`, `medication_id`, `position_date`), (`cnes`, `position_date`), `position_date`, `medication_id`, `integration_payload_id`.

### `external_stock_current`

Projeção consultável: uma linha por posição válida mais recente, só para registros com unidade e medicamento resolvidos. É o que o painel e a busca leem; o histórico fica fora das consultas comuns.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `external_stock_position_id` | FK, restrict, **único** | Posição de origem |
| `data_source_id`, `facility_id`, `medication_id` | FK, restrict | Fonte, unidade e medicamento |
| `lot_number`, `expires_on` | nulos | Lote e validade |
| `quantity` | decimal(14,3) | Quantidade |
| `position_date`, `imported_at` | date, timestamp | Copiadas da posição |
| `quality_status` | string(20) | Enum `StockQualityStatus`: `current`, `stale`, `inconsistent`, `quarantined`. "Sem informação" é ausência de linha, nunca zero |

Manter uma única linha vigente por unidade, medicamento e lote é responsabilidade da publicação da projeção (não há restrição no banco, porque `lot_number` e `expires_on` podem ser nulos). Índices: (`facility_id`, `medication_id`, `quality_status`), (`medication_id`, `quality_status`, `position_date`), `data_source_id`.

### `data_quality_issues`

Conflitos, inconsistências e decisões de revisão.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `data_source_id`, `integration_run_id`, `external_stock_position_id`, `facility_id`, `medication_id` | FK, set null, nulos | Contexto da ocorrência |
| `type` | string(40) | Enum `DataQualityIssueType`: `conflicting_values`, `unknown_facility`, `unmapped_medication`, `ambiguous_identity`, `invalid_record`, `contract_change`, `stale_position` |
| `severity` | string(20) | Enum `IssueSeverity`: `warning`, `error` |
| `status` | string(20) | Enum `DataQualityIssueStatus`: `open`, `reviewed`, `dismissed`, `fixed` |
| `message` | text | Descrição |
| `details` | json, nulo | Valores envolvidos |
| `reviewed_by`, `reviewed_at`, `resolution` | FK set null, timestamp, text, nulos | Revisão |

Índices: (`status`, `type`), `integration_run_id`, `external_stock_position_id`.

### `idempotency_keys`

Controle de repetição dos comandos que alteram compromissos ou movimentações.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `actor_key` | string(60) | Quem executou, como texto (ex.: `user:12`) |
| `operation` | string(60) | Comando (ex.: `reservations.store`) |
| `idempotency_key` | string(100) | Chave enviada pelo cliente |
| `content_hash` | string(64) | Hash do conteúdo: mesma chave com conteúdo diferente é conflito |
| `response_status`, `response_body` | smallint, json, nulos | Resultado devolvido na repetição |
| `completed_at` | timestamp, nulo | Conclusão |

Restrições: único (`actor_key`, `operation`, `idempotency_key`).

### `outbox_events`

Eventos gravados na mesma transação do comando e publicados depois por um processo com tentativas idempotentes.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `event_type` | string(80) | Ex.: `transfer.approved` |
| `aggregate_type`, `aggregate_id` | string(60), bigint | Entidade de origem |
| `payload` | json | Conteúdo do evento |
| `status` | string(20) | Enum `OutboxEventStatus`: `pending`, `published`, `failed` |
| `attempts` | smallint | Tentativas |
| `available_at`, `published_at` | timestamps | Disponível a partir de; publicado em |
| `last_error` | text, nulo | Última falha |

Índices: (`status`, `available_at`), (`aggregate_type`, `aggregate_id`).

### `audit_logs`

Alterações relevantes com ator, escopo e justificativa. Polimórfica (`auditable_type` guarda a classe do model).

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `user_id` | FK → `users`, set null | Ator |
| `action` | string(80) | Ex.: `offer.confirmed` |
| `auditable_type`, `auditable_id` | string(80), bigint | Entidade afetada |
| `municipality_id`, `facility_id` | FK, set null, nulos | Escopo da ação |
| `justification` | text, nulo | Motivo |
| `changes` | json, nulo | Antes e depois |
| `ip_address` | string(45), nulo | Origem |
| `occurred_at` | timestamp | Momento |

Índices: (`auditable_type`, `auditable_id`), (`user_id`, `occurred_at`), `occurred_at`, `facility_id`.

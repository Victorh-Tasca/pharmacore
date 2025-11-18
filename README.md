# 💊 PHARMACORE - Sistema de Gestão de Farmácia

[![Status](https://img.shields.io/badge/Status-Estável%20(CRUD%20Concluído)-2ecc71)](link-para-sua-url)
[![Linguagem Principal](https://img.shields.io/badge/PHP-8.3+-774BBF)](https://www.php.net/)
[![Banco de Dados](https://img.shields.io/badge/PostgreSQL-Supabase%20Pooler-336791)](https://www.postgresql.org/)

Sistema de gestão de estoque de medicamentos focado em controle de lotes, rastreabilidade e emissão de alertas operacionais. Construído com uma arquitetura PHP API-First e PostgreSQL.

***

## 🌟 VISÃO GERAL E MÓDULOS

| Módulo Principal | Entidades Cobertas | Estado Atual |
| :--- | :--- | :--- |
| **Autenticação** | Usuários, Papéis, Configurações | ✅ **Completo** |
| **Cadastros** | Laboratórios, Pacientes, Medicamentos, Fornecedores | ✅ **Completo** |
| **Estoque** | Entradas, Visualização de Lotes (`vw_estoque_por_lote`) | ✅ **Completo** |
| **Relatórios** | Geração de Alertas (Validade e Reposição) | ✅ **Completo** |

***

## 🛠️ SETUP E PRIMEIROS PASSOS

### 1. Requisitos Prévios

-   **PHP 8.x** (com extensão **`pdo_pgsql`** ativada no `php.ini`).
-   **PostgreSQL** (URL de conexão do Pooler do Supabase).
-   Adicionar o executável `php.exe` ao **PATH do Usuário**.

### 2. Configuração do `.env`

Crie o arquivo `.env` na raiz do projeto e use a string do Pooler:

```env
DATABASE_URL=postgresql://postgres.hcppdyfnkzhgvmgspeeq:SUA_SENHA_AQUI@aws-1-sa-east-1.pooler.supabase.com:5432/postgres 
```

***

### 3. Inicialização do Servidor

Abra o terminal na pasta raiz (PHARMACORE-MAIN/) e execute:

```bash
php -S localhost:8000
```

URL de Acesso: [http://localhost:8000/pharma-login/index.html](http://localhost:8000/pharma-login/index.html)

***

## 🛡️ ARQUITETURA E DECISÕES CHAVE

### 1. Segurança e Guardião de Rota

* **Guardião (FOUC Fix):** O script no `<head>` do HTML faz um `fetch` assíncrono para `/api/auth/session.php` e só torna o `<body>` visível após a confirmação (`200 OK`) da sessão, garantindo que usuários não logados não vejam o conteúdo.
* **Pathing:** Todos os caminhos de API e redirecionamentos no JavaScript são **absolutos** (ex: `/api/auth/login.php`), garantindo que funcionem em qualquer subpasta.

### 2. Integridade de Dados (Transações)

* **Transações:** As rotas que manipulam múltiplas tabelas (ex: criação de usuário, entrada de estoque) usam Transações PDO para garantir que as operações sejam atômicas.
* **Arquivamento:** O lote é marcado como inativo (`lotes.ativo = FALSE`) via chamada de função SQL, preservando o histórico de movimentação.

***

## 📜 TABELA DE ENDPOINTS IMPLEMENTADOS

| Módulo | Tipo | Rota | Função Principal |
| :--- | :--- | :--- | :--- |
| **Segurança** | `POST` | `/api/auth/session.php` | Verifica validade da sessão (Guardião) |
| **Admin** | `GET / POST` | `/api/usuarios/me.php` / `/update_me.php` | Leitura e Atualização do Perfil Logado |
| **Cadastros** | `GET / POST` | `/api/laboratorios/read.php` | CRUD Padrão para Entidades (Laboratórios, Pacientes, Classes) |
| **Estoque (Criação)** | `POST` | `/api/estoque/create_entrada.php` | Executa a transação de criação de Lote e Entrada. |
| **Estoque (Arquiv.)** | `POST` | `/api/estoque/archive_lote.php` | Marca o lote como inativo/arquivado. |
| **Dashboard** | `GET` | `/api/dashboard/read_stats.php` | Busca 4 métricas agregadas (COUNT de vencidos, estoque baixo, etc.) |
| **Relatórios** | `GET` | `/api/alertas/read.php` | Agrega alertas de validade e estoque baixo. |

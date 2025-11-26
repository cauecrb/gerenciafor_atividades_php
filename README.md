# Gerenciador de Atividades (Laravel + Inertia + Vue 3)

Aplicação para gerenciamento de projetos e tarefas, com controle de membros por projeto, tarefas pessoais, anexos em PDF, filtros por status e prioridade, e interface SPA via Inertia.

## Tecnologias
- Laravel 12 (PHP 8.4)
- Inertia.js + Vue 3
- Vite
- SQLite
- Ziggy (rotas no front)

## Recursos
- Projetos: criar, editar, excluir, anexar PDF, datas de início e previsão
- Membros de projeto: adicionar/remover (owner e membros têm acesso às tarefas do projeto)
- Tarefas do projeto: criar, editar, excluir, atribuir membros, vencimento, status
- Tarefas pessoais: CRUD completo, filtros por status/prioridade, anexar PDF, concluir tarefa

## Instalação e Execução
1. Instale dependências
   - `composer install`
   - `npm install`
2. Configure o ambiente
   - Copie `.env.example` para `.env` e ajuste variáveis
   - Gere a chave: `php artisan key:generate`
   - Crie banco SQLite: `mkdir database && type NUL > database\database.sqlite` (Windows)
3. Migrações
   - `php artisan migrate --force`
4. Rodar em desenvolvimento
   - Backend: `php artisan serve --host=localhost --port=8000`
   - Frontend: `npm run dev`
5. Acesso
   - Abra `http://localhost:8000/` e faça login/cadastro

## Estrutura de Dados (Tabelas)
- `users`
  - `id`, `name`, `email`, `password`, `timestamps`
- `projects`
  - `id`, `user_id` (owner), `title`, `description`, `start_date`, `due_date`, `attachment_path`, `timestamps`
- `tasks`
  - `id`, `user_id` (criador), `project_id` (nullable), `title`, `description`, `status` (pending|in_progress|completed), `priority` (low|medium|high), `due_at`, `completed_at`, `attachment_path`, `timestamps`
- `project_user` (membros do projeto)
  - `id`, `project_id`, `user_id`, `timestamps`, `unique(project_id,user_id)`
- `task_user` (atribuições de usuários às tarefas)
  - `id`, `task_id`, `user_id`, `timestamps`, `unique(task_id,user_id)`

## Regras de Acesso
- Somente owner e membros de um projeto podem ver/gerenciar as tarefas do projeto
- Tarefas pessoais são visíveis e gerenciáveis apenas pelo seu criador (`user_id`)

## Rotas Principais
- Projetos
  - `GET /projects` lista
  - `POST /projects` cria
  - `PUT /projects/{project}` atualiza
  - `DELETE /projects/{project}` exclui
  - `POST /projects/{project}/members` adiciona membro
  - `DELETE /projects/{project}/members/{user}` remove membro
  - `GET /projects/{project}/tasks` tarefas do projeto
- Tarefas Pessoais
  - `GET /tasks` lista com filtros
  - `GET /tasks/create` formulário de criação
  - `POST /tasks` cria (upload PDF em `public/storage/task_attachments`)
  - `GET /tasks/{task}/edit` edição
  - `PUT /tasks/{task}` atualiza
  - `DELETE /tasks/{task}` exclui
  - `POST /tasks/{task}/complete` marca como concluída

## DER (Diagrama de Entidades e Relacionamentos)
```mermaid
erDiagram
    USER ||--o{ PROJECT : owns
    PROJECT ||--o{ TASK : has
    USER ||--o{ TASK : creates

    PROJECT }o--o{ USER : members
    TASK }o--o{ USER : assigned

    USER {
        bigint id PK
        string name
        string email
        string password
    }
    PROJECT {
        bigint id PK
        bigint user_id FK
        string title
        text description
        date start_date
        date due_date
        string attachment_path
    }
    TASK {
        bigint id PK
        bigint user_id FK
        bigint project_id FK NULL
        string title
        text description
        string status
        string priority
        datetime due_at NULL
        datetime completed_at NULL
        string attachment_path NULL
    }
    PROJECT_USER {
        bigint id PK
        bigint project_id FK
        bigint user_id FK
        unique(project_id, user_id)
    }
    TASK_USER {
        bigint id PK
        bigint task_id FK
        bigint user_id FK
        unique(task_id, user_id)
    }
```

## Observações
- Uploads de tarefas são salvos em `public/storage/task_attachments` (acesso via `/storage/task_attachments/<arquivo>.pdf`).
- Formatação de datas no front padronizada para `dd/mm/aaaa`.

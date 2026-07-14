# Task Manager — Laravel 12 + Nuxt 3

Full-stack приложение для управления задачами с авторизацией, CRUD, фильтрацией, поиском, сортировкой и пагинацией.

## Технологии

### Backend
- **Laravel 12** (PHP 8.3+)
- **Laravel Sanctum** — авторизация через Bearer Token
- **SQLite** — база данных
- **Eloquent ORM**, Form Requests, Policies, API Resources, Services

### Frontend
- **Nuxt 3** (Vue 3, TypeScript)
- **Pinia** — state management
- **Nuxt UI** — UI-компоненты
- **VueUse** — утилиты
- **TailwindCSS** — стили

## Структура проекта

```
├── backend/          # Laravel 12 API
├── frontend/         # Nuxt 3 SPA
├── docker-compose.yml
├── .env.example
└── README.md
```

## Быстрый старт с Docker

```bash
docker compose up --build
```

После запуска:
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api

## Локальная установка (без Docker)

### Требования
- PHP 8.3+ с расширениями: pdo_sqlite, mbstring, openssl, curl, fileinfo
- Composer 2.x
- Node.js 22+
- npm 10+

### Backend

```bash
cd backend

# Установить зависимости
composer install

# Скопировать .env и сгенерировать ключ
cp .env.example .env
php artisan key:generate

# Настроить SQLite в .env
# DB_CONNECTION=sqlite
# Удалить/закомментировать DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Создать файл БД
touch database/database.sqlite

# Запустить миграции
php artisan migrate

# Создать тестового пользователя
php artisan db:seed

# Запустить сервер
php artisan serve
```

### Frontend

```bash
cd frontend

# Установить зависимости
npm install

# Запустить dev-сервер
npm run dev
```

Frontend запустится на http://localhost:3000, Backend API на http://localhost:8000.

## Тестовые пользователи

| Роль     | Email            | Пароль   |
|----------|------------------|----------|
| Admin    | admin@test.com   | password |
| User     | user@test.com    | password |

## Тестирование (Testing)

Проект полностью покрыт автоматическими тестами как на бэкенде, так и на фронтенде.

### Запуск тестов Backend (Laravel API)

Для запуска Unit/Feature тестов перейдите в папку `backend` и выполните PHPUnit:

```bash
cd backend
vendor/bin/phpunit
```

Тесты покрывают:
- Разграничение прав доступа (TaskPermissionTest): Администратор видит все задачи, обычный пользователь видит только свои задачи.
- Процесс аутентификации (AuthTest): Успешный вход, обработка неверных кредов, получение профиля текущего пользователя, логаут.
- Фильтрация и поиск (TaskFilterSearchTest): Поиск задач по тексту в названии/описании, фильтрация по статусу, пагинация и сортировка.
- Валидация входных данных (TaskValidationTest): Проверка обязательности полей, минимальной/максимальной длины и соответствия enum статусов.

### Запуск тестов Frontend (Nuxt 3)

Для запуска тестов фронтенда перейдите в папку `frontend` и запустите Vitest:

```bash
cd frontend
npm run test
```

Тесты покрывают:
- Auth Store (auth.test.ts): Инициализация хранилища, авторизация и логаут с очисткой токена и перенаправлением.
- Tasks Store (tasks.test.ts): Логика фильтрации, пагинации, сортировки и автоматического обновления списка задач.
- Компонент поиска (TaskSearch.test.ts): Синхронизация поиска с URL-параметрами, реактивное обновление состояния при вводе и очистка.

## Документация API (OpenAPI / Swagger)

Полная интерактивная документация API доступна в формате OpenAPI 3.0 в файле `openapi.yaml` в корне проекта.

Вы можете импортировать файл `openapi.yaml` в Swagger Editor, Postman или использовать любой другой инструмент просмотра Swagger UI.

## API Endpoints

### Авторизация

| Метод | URL               | Описание          | Auth |
|-------|-------------------|--------------------|------|
| POST  | /api/auth/login   | Логин              | ❌   |
| POST  | /api/auth/logout  | Логаут             | ✅   |
| GET   | /api/user         | Текущий пользователь | ✅ |

### Задачи (Tasks)

| Метод  | URL              | Описание          | Auth |
|--------|------------------|--------------------|------|
| GET    | /api/tasks       | Список задач       | ✅   |
| GET    | /api/tasks/{id}  | Одна задача        | ✅   |
| POST   | /api/tasks       | Создать задачу     | ✅   |
| PATCH  | /api/tasks/{id}  | Обновить задачу    | ✅   |
| DELETE | /api/tasks/{id}  | Удалить задачу     | ✅   |

### Параметры списка задач

| Параметр       | Описание                                    |
|----------------|---------------------------------------------|
| search         | Поиск по title и description                |
| status         | Фильтр: pending, in_progress, completed     |
| sort_by        | Сортировка: due_date, status, created_at    |
| sort_direction | Направление: asc, desc                     |
| page           | Номер страницы                               |
| per_page       | Кол-во на странице (max 100)                 |

## Архитектура Backend

```
app/
├── Enums/           # TaskStatus enum
├── Http/
│   ├── Controllers/Api/  # AuthController, TaskController
│   ├── Requests/         # LoginRequest, StoreTaskRequest, UpdateTaskRequest
│   └── Resources/        # TaskResource, UserResource
├── Models/          # User, Task
├── Policies/        # TaskPolicy
└── Services/        # TaskService
```

**Принципы:**
- Бизнес-логика в **Services**, не в контроллерах
- Валидация через **Form Requests**
- Авторизация через **Policies** (без проверок в контроллере)
- Единообразные ответы через **API Resources**
- Статусы через **PHP Enum** (TaskStatus)

## Архитектура Frontend

```
frontend/
├── api/             # HTTP-запросы (auth.ts, tasks.ts)
├── composables/     # useApiFetch — HTTP-клиент
├── stores/          # Pinia stores (auth, tasks)
├── types/           # TypeScript типы
├── middleware/       # auth middleware
├── components/
│   ├── task/        # TaskCard, TaskForm, TaskModal, TaskSearch, TaskFilters, TaskTable
│   └── ui/          # Pagination, EmptyState, ConfirmDialog, Loader
├── layouts/         # default layout
└── pages/           # login, tasks
```

**Принципы:**
- Все HTTP-запросы через **API слой**, не из компонентов напрямую
- Строгая **TypeScript** типизация
- **Composition API** + Pinia
- Optimistic updates при удалении и смене статуса
- Debounced поиск

## Авторизация

Используется **Laravel Sanctum** с Bearer Token:

1. `POST /api/auth/login` — возвращает `{ token, user }`
2. Токен сохраняется в cookie `auth_token`
3. Все защищённые запросы отправляются с заголовком `Authorization: Bearer {token}`
4. Middleware на фронтенде перенаправляет неавторизованных на `/login`

## Обработка ошибок

### Backend
Все ошибки возвращаются в JSON формате:
- **401** — Unauthorized (невалидный/отсутствующий токен)
- **403** — Forbidden (нет доступа к чужой задаче)
- **404** — Not Found
- **422** — Validation Error (с полями `message` и `errors`)
- **500** — Server Error

### Frontend
- Toast-уведомления для всех ошибок API
- Отображение ошибок валидации в формах
- Перенаправление на login при 401

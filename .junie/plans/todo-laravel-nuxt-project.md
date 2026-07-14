---
sessionId: session-260710-123929-1rvg
---

# Requirements

### Overview & Goals
The goal is to build a high-quality, full-stack Todo application using **Laravel 12** and **Nuxt 3**. The project will demonstrate modern development practices, including a service-oriented architecture, strict typing, and a containerized environment.

### Scope
- **Backend**: Laravel 12 API with Sanctum, SQLite, and SOLID principles.
- **Frontend**: Nuxt 3 SPA with Pinia, TypeScript, and a responsive UI.
- **Infrastructure**: Docker Compose for easy setup.
- **Features**: Authentication, Task CRUD with search/filter/sort, and user-level isolation.

### Functional Requirements
- Secure user authentication (Bearer Token).
- Task management (Title, Description, Due Date, Status).
- Advanced listing (Search, Filtering by status, Sorting, Pagination).
- User isolation (Users only see/edit their own tasks).
- Responsive and modern UI with feedback (Toasts, Skeletons).

### Non-Functional Requirements
- **Code Quality**: Strict typing, no magic strings, Laravel Pint & ESLint compliance.
- **Performance**: Optimistic UI updates and efficient backend queries.
- **Maintainability**: Clear separation of concerns (Services, Resources, Policies).

# Technical Design

### Architecture Overview
The project follows a decoupled client-server architecture, communicating via a RESTful API.

### Backend (Laravel 12)
- **Service Layer**: Business logic resides in `TaskService`, keeping controllers thin.
- **Enums**: `TaskStatus` (Pending, In Progress, Completed) implemented as PHP Enums.
- **Authorization**: `TaskPolicy` ensures data privacy at the Eloquent level.
- **API Resources**: Unified JSON structure for all responses.

### Frontend (Nuxt 3)
- **State Management**: Pinia stores for `Auth` and `Tasks`.
- **API Layer**: Centralized API client handling auth headers and error interceptors.
- **Components**: Atomic design approach for UI components (Table, Modal, Card).

### Database Schema
**Tasks Table:**
- `id` (UUID or BigInt)
- `user_id` (Foreign Key)
- `title` (String)
- `description` (Text, Nullable)
- `due_date` (Date/DateTime, Nullable)
- `status` (Enum: pending, in_progress, completed)
- Timestamps

### Docker Setup
- `backend`: PHP 8.3/8.4-FPM + Nginx (or Artisan serve for simplicity).
- `frontend`: Node 20+ (Vite dev server or Nitro production).
- `sqlite`: Persistent volume for the database.

# Testing

### Validation Approach
Verification will be done manually and via smoke tests in the Docker environment.

### Key Scenarios
1. **Auth Flow**: Register/Login, check token persistence, logout.
2. **CRUD Flow**: Create task, view in list, edit status, delete.
3. **Filtering**: Search by keyword and filter by status simultaneously.
4. **Security**: Attempting to access another user's task ID (should return 403).

### Edge Cases
- Empty search results (EmptyState display).
- Validation errors (422) handled by showing clear Toast messages.
- Network latency (Skeleton screens during loading).
- Unauthorized access (Redirect to /login).

# Delivery Steps

### ✓ Step 1: Project Initialization & Docker Setup
Initialize the root project structure and Docker environment.

- Create `backend/`, `frontend/`, and `README.md`.
- Configure `docker-compose.yml` with Laravel (PHP 8.3/8.4), Nuxt 3, and SQLite volume.
- Setup `.env.example` and basic Dockerfiles for both services.

### * Step 2: Backend Auth & User Management
Set up the Laravel 12 backend with authentication.

- Install Laravel 12 and Laravel Sanctum.
- Implement Login/Logout endpoints and `GET /api/user`.
- Create the `UserSeeder` with the specified admin credentials.
- Set up standard API response wrapper and exception handler.

###   Step 3: Task Backend Implementation
Implement Task CRUD and business logic on the backend.

- Create `Task` model, `TaskStatus` Enum, and migration.
- Develop `TaskService` to handle CRUD operations.
- Implement `TaskPolicy` for user-specific access control.
- Add Search, Filter, Sort, and Pagination logic using the chosen architectural pattern.
- Create FormRequests and API Resources for structured data.

###   Step 4: Frontend Auth & Core Setup
Set up the Nuxt 3 frontend with authentication.

- Initialize Nuxt 3 with TailwindCSS and the selected UI library.
- Implement the API layer (`api/auth.ts`, `api/tasks.ts`) and Auth Store (Pinia).
- Create the Login page and guest/auth middleware.
- Configure Toast notifications and global error handling for API responses.

###   Step 5: Frontend Task Management UI
Implement Task management UI and features.

- Create `TaskTable`, `TaskCard`, and `TaskFilters` components.
- Develop the Task dashboard with search, filtering, and pagination.
- Implement Task creation and editing via `TaskModal` and `TaskForm`.
- Add task deletion with `ConfirmDialog` and optimistic updates.
- Implement skeletons and loading states for a smooth UX.

###   Step 6: Finalization & Documentation
Final polish, linting, and documentation.

- Run Laravel Pint for backend code style.
- Run ESLint and Prettier for frontend code style.
- Complete the README with installation and architecture details.
- Verify the full flow (CRUD, Auth, Filters) in the Docker environment.
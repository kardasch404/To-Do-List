# To-Do List Application

### Backend
- **Laravel 10** 
- **JWT Authentication** 
- **MySQL** 
- **Repository Pattern** 

### Frontend
- **Vue.js 3** 
- **Axios** 

## 📁 Project Structure

```
To-Do-List/
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   └── TaskController.php
│   │   │   └── Requests/
│   │   │       ├── LoginRequest.php
│   │   │       ├── RegisterRequest.php
│   │   │       ├── TaskRequest.php
│   │   │       └── TaskUpdateRequest.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   └── Task.php
│   │   └── Repositories/
│   │       ├── UserRepository.php
│   │       └── TaskRepository.php
│   └── routes/
│       └── api.php
└── frontend/
    ├── src/
    │   ├── components/
    │   │   ├── LoginForm.vue
    │   │   ├── RegisterForm.vue
    │   │   ├── KanbanBoard.vue
    │   │   ├── NotificationCenter.vue
    │   │   └── NotificationsPage.vue
    │   ├── services/
    │   │   ├── api.js
    │   │   └── echo.js
    │   └── App.vue
    └── package.json
```

## 🔧 Installation

### Prerequisites
- PHP 8
- Composer
- Node.js
- MySQL
- Laragon

### Backend Setup

1. **Clone the repository**
   ```bash
   cd C:\laragon\www
   git clone https://github.com/kardasch404/To-Do-List.git
   cd To-Do-List/backend
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=to-do-list
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **JWT Configuration**
   ```bash
   php artisan jwt:secret
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Create storage link**
   ```bash
   php artisan storage:link
   ```

8. **Start the server**
   ```bash
   php artisan serve
   ```

### Frontend Setup

1. **Navigate to frontend directory**
   ```bash
   cd ../frontend
   ```

2. **Install Node.js dependencies**
   ```bash
   npm install
   ```

3. **Install additional packages**
   ```bash
   npm install laravel-echo pusher-js
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

## 🔌 API Endpoints

### Authentication Routes
```
POST /api/auth/register - User registration
POST /api/auth/login    - User login
POST /api/auth/logout   - User logout
```

### Task Routes
```
GET    /api/tasks      - Get all user tasks
POST   /api/tasks      - Create new task
PUT    /api/tasks/{id} - Update task
DELETE /api/tasks/{id} - Delete task
```

## Database Schema

### Users Table
```sql
- id (Primary Key)
- full_name (String)
- email (String, Unique)
- phone_number (String, Nullable)
- address (String, Nullable)
- image (String, Nullable)
- mot_de_passe (String) - Encrypted password
- created_at, updated_at (Timestamps)
```

### Tasks Table
```sql
- id (Primary Key)
- title (String)
- description (Text, Nullable)
- is_done (Boolean, Default: false)
- user_id (Foreign Key)
- created_at, updated_at (Timestamps)
```

## Frontend Components

### Main Components
- **LoginForm.vue** 
- **RegisterForm.vue** 
- **KanbanBoard.vue** 
- **NotificationsPage.vue** 
- **NotificationCenter.vue** 

## Task Status Flow

```
To Do (pending) → Doing (in_progress) → Done (completed)
```




---


# 📋 To-Do List Application

A modern, full-stack task management application built with Laravel (Backend) and Vue.js (Frontend) featuring real-time notifications, drag-and-drop functionality, and secure JWT authentication.

## Features

- **User Authentication**: Secure registration and login with JWT tokens
- **Task Management**: Create, read, update, and delete tasks
- **Kanban Board**: Drag and drop tasks between columns (To Do, Doing, Done)
- **Real-time Notifications**: Get notified when tasks are created, updated, or deleted
- **User Profiles**: Profile management with image upload support
- **Responsive Design**: Modern UI with custom color scheme (#FFD900 & #20252A)
- **File Upload**: Support for user profile images

## Tech Stack

### Backend
- **Laravel 10+** - PHP Framework
- **JWT Authentication** - Secure token-based authentication
- **MySQL** - Database
- **Repository Pattern** - Clean architecture
- **File Storage** - Image upload handling

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Axios** - HTTP client for API calls
- **Drag & Drop** - Native HTML5 drag and drop
- **Local Storage** - Client-side data persistence
- **Responsive CSS** - Custom styling

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
- PHP 8.1+
- Composer
- Node.js
- MySQL
- Laragon

### Backend Setup

1. **Clone the repository**
   ```bash
   cd C:\laragon\www
   git clone <https://github.com/kardasch404/To-Do-List.git> To-Do-List
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

## 📊 Database Schema

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

## 🎨 Frontend Components

### Main Components
- **LoginForm.vue** - User authentication form
- **RegisterForm.vue** - User registration form
- **KanbanBoard.vue** - Main task management interface
- **NotificationsPage.vue** - Dedicated notifications page
- **NotificationCenter.vue** - Notification popup component

### Key Features
- **Drag & Drop**: Tasks can be moved between columns
- **Real-time Updates**: Notifications appear instantly
- **User Avatar**: Displays user initials in circular avatar
- **Responsive Design**: Works on desktop and mobile

## Authentication Flow

1. **Registration**: User creates account with profile image
2. **Login**: JWT token generated and stored
3. **API Requests**: Token sent in Authorization header
4. **Logout**: Token invalidated on server

## 📱 Usage

1. **Register/Login**: Create account or sign in
2. **Create Tasks**: Add new tasks using the form
3. **Manage Tasks**: Drag tasks between columns (To Do → Doing → Done)
4. **View Notifications**: Click notification bell to see updates
5. **Profile Menu**: Click avatar to access profile and logout

## Task Status Flow

```
To Do (pending) → Doing (in_progress) → Done (completed)
```

- **To Do**: New tasks (is_done: false, status: pending)
- **Doing**: In progress (is_done: false, status: in_progress)  
- **Done**: Completed (is_done: true, status: completed)

## 🔧 Configuration

### Backend Configuration
- JWT secret in `.env`
- Database connection settings
- File storage configuration
- CORS settings for frontend

### Frontend Configuration
- API base URL in `services/api.js`
- Echo/Pusher configuration for real-time features
- Color scheme variables in CSS


---


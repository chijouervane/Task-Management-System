# Task Management System

## Project Overview
A web application for managing tasks, allowing users to create, update, track, and delete tasks efficiently.

## Features
- Create new tasks
- Update existing tasks
- Mark tasks as pending or completed
- Delete tasks
- Filter tasks by status

## Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js
- npm
- MySQL Database

## Installation Steps

### Backend Setup
1. Clone the repository
```bash
git clone https://github.com/chijouervane/task-management-system.git
cd task-management-system/backend
```

2. Install PHP dependencies
```bash
composer install
```

3. Configure Environment
```bash
cp .env.example .env
# Edit .env file with your database credentials
```

4. Database Setup
```bash
php artisan migrate
```

### Frontend Setup
```bash
cd ../frontend
npm install
```

## Running the Application
1. Start Backend Server
```bash
cd backend
php artisan serve
```

2. Start Frontend Server
```bash
cd frontend
npm run dev
```

## Backend Tests
```bash
cd backend
php artisan test
```

## Technologies Used
- Backend: Laravel
- Frontend: Vue.js
- Database: MySQL

## API Endpoints
- `GET /api/tasks`: List tasks
- `POST /api/tasks`: Create task
- `PUT /api/tasks/{id}`: Update task
- `DELETE /api/tasks/{id}`: Delete task
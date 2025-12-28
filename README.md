# TodoList Application

A full-stack todolist application built with PHP, MySQL, React, and Bootstrap.

## Features

- Create multiple todo lists with unique hash IDs
- Add, check/uncheck, and manage tasks within each list
- Edit todo list titles
- Responsive design using Twitter Bootstrap
- RESTful API backend with PHP and MySQL

## Technology Stack

- **Backend**: PHP, MySQL
- **Frontend**: React 18, Twitter Bootstrap 5
- **Build Tool**: Webpack

## Project Structure

```
todolist/
├── backend/
│   ├── api.php          # Main API endpoints
│   ├── config.php       # Database configuration
│   └── schema.sql       # Database schema
└── frontend/
    ├── public/
    │   └── index.html
    ├── src/
    │   ├── App.js       # Main React component
    │   ├── App.css      # Styles
    │   └── index.js     # Entry point
    ├── package.json
    └── webpack.config.js
```

## Installation

### Option 1: Using Docker (Recommended)

This is the easiest way to get started. Docker will set up everything for you.

#### Prerequisites
- Docker and Docker Compose

#### Steps

1. Start the application:
```bash
docker-compose up -d
```

2. Wait for all services to start (this may take a minute on first run)

3. Access the application:
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8080
   - Database: localhost:3306

4. To stop the application:
```bash
docker-compose down
```

### Option 2: Manual Installation

#### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Node.js 14 or higher
- npm or yarn

#### Backend Setup

1. Create the database:
```bash
mysql -u root -p < backend/schema.sql
```

2. Configure database connection in `backend/config.php` if needed (default uses localhost, root user, no password)

3. Set up a local PHP server or use Apache/Nginx to serve the backend folder
   - The API should be accessible at `http://localhost/todolist/backend/api.php`
   - Or adjust the API_URL in `frontend/src/App.js` to match your setup

### Frontend Setup

1. Navigate to the frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm start
```

The application will open in your browser at `http://localhost:3000`

## Usage

1. **Main Screen**: Click the "ADD" button to create a new todo list
2. **Todo List View**: 
   - The title is auto-generated but can be edited by clicking on it
   - Click "ADD Item" to open a popup and add a new task
   - Check the checkbox next to tasks to mark them as done
   - Click "Back" to return to the main screen

## API Endpoints

- `POST /api.php?path=create` - Create a new todolist
- `GET /api.php?path=todolist/{hash_id}` - Get a specific todolist with its items
- `GET /api.php?path=todolists` - Get all todolists
- `POST /api.php?path=add-item` - Add an item to a todolist
- `POST /api.php?path=toggle-item` - Toggle item done status
- `POST /api.php?path=update-title` - Update todolist title

## Building for Production

```bash
cd frontend
npm run build
```

This will create a `dist` folder with the production-ready files.

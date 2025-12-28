# Application Architecture

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         User Browser                          │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │           React Frontend (Port 3000)                     │ │
│  │                                                           │ │
│  │  • Main Screen (Create TodoList)                         │ │
│  │  • TodoList View (Edit Title, Add Items)                 │ │
│  │  • Bootstrap UI Components                               │ │
│  │  • Modal Popup for Adding Tasks                          │ │
│  └─────────────────────────────────────────────────────────┘ │
│                            ↓                                  │
│                       HTTP/AJAX                               │
│                            ↓                                  │
└─────────────────────────────────────────────────────────────┘
                             ↓
┌─────────────────────────────────────────────────────────────┐
│                 PHP Backend API (Port 8080)                   │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                    api.php                               │ │
│  │                                                           │ │
│  │  POST /api.php?path=create        - Create todolist     │ │
│  │  GET  /api.php?path=todolists     - Get all lists       │ │
│  │  GET  /api.php?path=todolist/{id} - Get specific list   │ │
│  │  POST /api.php?path=add-item      - Add item to list    │ │
│  │  POST /api.php?path=toggle-item   - Mark item done      │ │
│  │  POST /api.php?path=update-title  - Update list title   │ │
│  └─────────────────────────────────────────────────────────┘ │
│                            ↓                                  │
│                        mysqli                                 │
│                            ↓                                  │
└─────────────────────────────────────────────────────────────┘
                             ↓
┌─────────────────────────────────────────────────────────────┐
│                  MySQL Database (Port 3306)                   │
│                                                               │
│  ┌──────────────────────┐    ┌──────────────────────┐       │
│  │   todolists          │    │   todo_items         │       │
│  ├──────────────────────┤    ├──────────────────────┤       │
│  │ id (INT)             │    │ id (INT)             │       │
│  │ hash_id (VARCHAR)    │◄───┤ todolist_hash        │       │
│  │ title (VARCHAR)      │    │ task (TEXT)          │       │
│  │ created_at           │    │ is_done (BOOL)       │       │
│  └──────────────────────┘    │ created_at           │       │
│                               └──────────────────────┘       │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow

### Creating a TodoList

1. User clicks "ADD" button on main screen
2. Frontend sends POST request to `/api.php?path=create`
3. Backend generates a unique cryptographically secure hash as todolist ID
4. Backend inserts new record into `todolists` table
5. Backend returns `hash_id` and `title` to frontend
6. Frontend navigates to todolist view

### Adding a Task

1. User clicks "ADD Item" button
2. Modal popup appears with text input
3. User enters task and clicks "Add Task"
4. Frontend sends POST to `/api.php?path=add-item` with hash_id and task
5. Backend inserts into `todo_items` table
6. Backend returns new item data
7. Frontend updates item list

### Checking/Unchecking Tasks

1. User clicks checkbox next to task
2. Frontend sends POST to `/api.php?path=toggle-item` with item_id and new status
3. Backend updates `is_done` field in database
4. Frontend updates UI (strikethrough, gray color)

## Key Features

### Hash-based IDs
- Each todolist has a unique cryptographically secure hash as identifier
- 32-character hexadecimal string generated from random_bytes()
- Provides security through obscurity and collision resistance
- Makes URLs shareable and bookmarkable

### Responsive Design
- Bootstrap 5 for mobile-first responsive layout
- Works on desktop, tablet, and mobile devices
- Modal popups adapt to screen size

### Real-time Updates
- Changes are immediately reflected in UI
- Direct API calls without page refresh
- Optimistic UI updates

### Editable Titles
- Click on title to edit
- Press Enter or click away to save
- Auto-generated default titles

## Technology Stack Details

### Frontend
- **React 18**: Modern UI library with hooks
- **Bootstrap 5**: Responsive CSS framework
- **Webpack**: Module bundler
- **Babel**: JavaScript transpiler

### Backend
- **PHP 7.4+**: Server-side language
- **MySQLi**: Database driver
- **JSON**: Data exchange format
- **CORS**: Cross-origin resource sharing enabled

### Database
- **MySQL 8.0**: Relational database
- **InnoDB**: Storage engine with foreign key support
- **UTF8MB4**: Character set for full Unicode support

### DevOps
- **Docker**: Containerization
- **Docker Compose**: Multi-container orchestration
- **Git**: Version control

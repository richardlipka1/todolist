# Quick Start Guide

## Using Docker (Easiest Method)

1. **Install Docker Desktop** (if not already installed)
   - Download from: https://www.docker.com/products/docker-desktop

2. **Clone the repository**
   ```bash
   git clone https://github.com/richardlipka1/todolist.git
   cd todolist
   ```

3. **Start the application**
   ```bash
   docker-compose up -d
   ```

4. **Access the application**
   - Open your browser and go to: http://localhost:3000
   - The backend API is available at: http://localhost:8080

5. **Stop the application**
   ```bash
   docker-compose down
   ```

## Manual Setup (Alternative Method)

### 1. Setup Database

```bash
mysql -u root -p < backend/schema.sql
```

### 2. Configure Backend

Edit `backend/config.php` if your MySQL credentials are different from the defaults.

### 3. Setup PHP Server

**Option A - Using PHP Built-in Server:**
```bash
cd backend
php -S localhost:8080
```

**Option B - Using Apache/Nginx:**
Place the project in your web server's document root and configure a virtual host.

### 4. Setup Frontend

```bash
cd frontend
npm install
npm start
```

The application will open at http://localhost:3000

## First Steps

1. **Create a TodoList**
   - Click the "ADD" button on the main screen
   - A new todolist with an auto-generated title will be created

2. **Edit the Title**
   - Click on the title to edit it
   - Press Enter or click away to save

3. **Add Tasks**
   - Click "ADD Item" button
   - Enter your task in the popup
   - Click "Add Task" or press Enter

4. **Complete Tasks**
   - Check the checkbox next to a task to mark it as done
   - Uncheck to mark it as not done

5. **Navigate**
   - Click "Back" to return to the main screen
   - View all your todolists from the main screen

## Troubleshooting

### Frontend can't connect to backend

If you see network errors in the browser console:

1. Check that the backend is running at http://localhost:8080
2. Update the API_URL in `frontend/src/App.js` if your backend is at a different location:
   ```javascript
   const API_URL = 'http://localhost:8080/api.php';
   ```

### Database connection errors

1. Verify MySQL is running
2. Check credentials in `backend/config.php`
3. Ensure the database was created: `mysql -u root -p -e "SHOW DATABASES;"`

### Port already in use

If port 3000 or 8080 is already in use:

**For Docker:**
Edit `docker-compose.yml` and change the port mappings:
```yaml
ports:
  - "3001:3000"  # Change 3001 to any available port
```

**For manual setup:**
Start the frontend on a different port:
```bash
PORT=3001 npm start
```

## Next Steps

- Read the full [README.md](README.md) for detailed documentation
- Check [TESTING.md](TESTING.md) for comprehensive testing guide
- Review the API endpoints in `backend/api.php`
- Customize the styling in `frontend/src/App.css`

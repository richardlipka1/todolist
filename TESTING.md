# Testing Guide

## Manual Testing Instructions

Since this application requires a local PHP and MySQL setup, here's how to test it:

### Prerequisites Test
1. Verify PHP is installed: `php --version` (should be 7.4+)
2. Verify MySQL is installed: `mysql --version` (should be 5.7+)
3. Verify Node.js is installed: `node --version` (should be 14+)

### Backend Setup and Testing

1. **Database Setup**
   ```bash
   mysql -u root -p < backend/schema.sql
   ```
   This creates the `todolist_db` database with `todolists` and `todo_items` tables.

2. **Test Database Connection**
   Navigate to `http://localhost/todolist/backend/index.php` to see:
   - Database connection status
   - Tables existence verification
   - Current todolist count

3. **Test API Endpoints**
   
   Create a todolist:
   ```bash
   curl -X POST http://localhost/todolist/backend/api.php?path=create \
     -H "Content-Type: application/json" \
     -d '{"title":"Test List"}'
   ```
   
   Get all todolists:
   ```bash
   curl http://localhost/todolist/backend/api.php?path=todolists
   ```

### Frontend Setup and Testing

1. **Install Dependencies**
   ```bash
   cd frontend
   npm install
   ```

2. **Start Development Server**
   ```bash
   npm start
   ```
   This opens `http://localhost:3000` in your browser.

3. **Build for Production**
   ```bash
   npm run build
   ```
   Creates optimized files in `frontend/dist/`

### Feature Testing Checklist

#### Main Screen
- [ ] Main screen displays with "TodoList Manager" heading
- [ ] "ADD" button is visible and centered
- [ ] Clicking "ADD" creates a new todolist with generated title
- [ ] Existing todolists are displayed in a list (if any exist)
- [ ] Clicking on an existing todolist loads it

#### TodoList View
- [ ] Displays the todolist title (auto-generated with date)
- [ ] Title can be edited by clicking on it
- [ ] Pressing Enter or clicking away saves the title
- [ ] Hash ID is displayed below the title
- [ ] "Back" button returns to main screen
- [ ] "ADD Item" button is visible in the top right

#### Adding Items
- [ ] Clicking "ADD Item" opens a popup modal
- [ ] Modal has a text field labeled "Enter task description"
- [ ] Modal has "Cancel" and "Add Task" buttons
- [ ] Pressing Enter in the text field adds the task
- [ ] Empty tasks cannot be added
- [ ] New tasks appear in the list immediately

#### Item List
- [ ] When empty, shows message "No items yet..."
- [ ] Items are displayed in a list format
- [ ] Each item has a checkbox on the left
- [ ] Checking a checkbox marks the item as done
- [ ] Done items show with strikethrough text
- [ ] Done items appear in gray color
- [ ] Unchecking restores the item to normal state

#### Responsive Design
- [ ] Application is responsive on mobile devices
- [ ] Bootstrap styling is applied correctly
- [ ] Buttons and inputs are properly sized
- [ ] Modal popup displays correctly on all screen sizes

### API Response Examples

**Create Todolist Response:**
```json
{
  "success": true,
  "hash_id": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6",
  "title": "Todo List 12/28/2025"
}
```

**Get Todolist Response:**
```json
{
  "todolist": {
    "id": 1,
    "hash_id": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6",
    "title": "Todo List 12/28/2025",
    "created_at": "2025-12-28 17:54:00"
  },
  "items": [
    {
      "id": 1,
      "todolist_hash": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6",
      "task": "Buy groceries",
      "is_done": false,
      "created_at": "2025-12-28 17:55:00"
    }
  ]
}
```

### Troubleshooting

**Frontend cannot connect to backend:**
- Verify the API_URL in `frontend/src/App.js` matches your PHP server location
- Check that CORS headers are properly set in `backend/config.php`
- Ensure PHP server is running and accessible

**Database connection fails:**
- Check MySQL credentials in `backend/config.php`
- Verify MySQL service is running: `sudo systemctl status mysql`
- Ensure database was created: `mysql -u root -p -e "SHOW DATABASES;"`

**npm install fails:**
- Clear npm cache: `npm cache clean --force`
- Delete `node_modules` and `package-lock.json`, then retry
- Update Node.js to a newer version

## Automated Testing

For production use, consider adding:
- PHPUnit tests for backend API
- Jest/React Testing Library tests for frontend components
- E2E tests with Cypress or Playwright

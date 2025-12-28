# Usage Examples

## Example 1: Creating Your First TodoList

### Step 1: Start the Application
```bash
docker-compose up -d
```
Wait a moment for services to start, then open http://localhost:3000

### Step 2: Create a TodoList
- You'll see the main screen with "TodoList Manager" heading
- Click the blue "ADD" button
- A new todolist is created with title like "Todo List 12/28/2025"
- You're automatically taken to the todolist view

### Step 3: Customize the Title
- Click on the title "Todo List 12/28/2025"
- The title becomes editable
- Change it to "Shopping List" or any name you prefer
- Press Enter or click away to save

### Step 4: Add Your First Task
- Click the green "ADD Item" button in the top right
- A popup appears with a text field
- Type "Buy milk" and press Enter (or click "Add Task")
- The task appears in your list with an empty checkbox

### Step 5: Add More Tasks
Add several more tasks:
- "Buy eggs"
- "Buy bread"
- "Buy coffee"

### Step 6: Complete Tasks
- Go shopping and buy milk
- Come back and check the checkbox next to "Buy milk"
- The text becomes gray with a strikethrough
- Your shopping list now shows what's done!

## Example 2: Multiple TodoLists

### Creating Different Lists

**Shopping List:**
1. Create a new todolist (from main screen)
2. Name it "Shopping List"
3. Add items:
   - Buy groceries
   - Pick up dry cleaning
   - Get stamps at post office

**Work Tasks:**
1. Go back to main screen (click "Back")
2. Create another todolist
3. Name it "Work Tasks"
4. Add items:
   - Review pull requests
   - Update documentation
   - Prepare presentation

**Home Projects:**
1. Create a third todolist
2. Name it "Home Projects"
3. Add items:
   - Fix leaky faucet
   - Paint bedroom
   - Clean garage

### Switching Between Lists
- Click "Back" to see all your todolists on the main screen
- Click on any todolist to view and manage it
- Each todolist maintains its own items independently

## Example 3: Hash ID Usage

Each todolist has a unique hash ID displayed below the title. Example:
```
Shopping List
ID: a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

### Sharing TodoLists
You can bookmark or share the URL:
```
http://localhost:3000/?list=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

Note: You would need to implement URL routing in App.js to support this feature.

## Example 4: API Usage with curl

### Create a TodoList
```bash
curl -X POST http://localhost:8080/api.php?path=create \
  -H "Content-Type: application/json" \
  -d '{"title":"API Created List"}'
```

Response:
```json
{
  "success": true,
  "hash_id": "abc123def456...",
  "title": "API Created List"
}
```

### Add Items to the List
```bash
curl -X POST http://localhost:8080/api.php?path=add-item \
  -H "Content-Type: application/json" \
  -d '{"hash_id":"abc123def456...","task":"Task from API"}'
```

### Get TodoList Data
```bash
curl http://localhost:8080/api.php?path=todolist/abc123def456...
```

Response:
```json
{
  "todolist": {
    "id": 1,
    "hash_id": "abc123def456...",
    "title": "API Created List",
    "created_at": "2025-12-28 17:54:00"
  },
  "items": [
    {
      "id": 1,
      "todolist_hash": "abc123def456...",
      "task": "Task from API",
      "is_done": false,
      "created_at": "2025-12-28 17:55:00"
    }
  ]
}
```

### Toggle Item Status
```bash
curl -X POST http://localhost:8080/api.php?path=toggle-item \
  -H "Content-Type: application/json" \
  -d '{"item_id":1,"is_done":true}'
```

## Example 5: Typical Daily Workflow

### Morning
1. Open the app
2. Create "Today's Tasks"
3. Add items:
   - Morning exercise ✓
   - Check emails ✓
   - Team standup at 9am
   - Review code PRs

### During the Day
- Check off tasks as you complete them
- Add new tasks as they come up
- Edit the title to add the date if needed

### Evening
- Review what you accomplished (checked items)
- See what's left for tomorrow (unchecked items)
- Create a new list for tomorrow if needed

## Example 6: Team Collaboration Scenario

### Project Manager Creates Sprint Tasks
```javascript
// Using the API programmatically
const createTodoList = async () => {
  const response = await fetch('http://localhost:8080/api.php?path=create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ title: 'Sprint 23 Tasks' })
  });
  const data = await response.json();
  return data.hash_id;
};

const addTask = async (hashId, task) => {
  await fetch('http://localhost:8080/api.php?path=add-item', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ hash_id: hashId, task })
  });
};

// Usage
const hashId = await createTodoList();
await addTask(hashId, 'Complete user authentication');
await addTask(hashId, 'Implement search feature');
await addTask(hashId, 'Fix responsive design issues');
```

### Team Members Update Progress
Each team member can:
1. Access the todolist via shared hash ID
2. Check off tasks they complete
3. Add new tasks they discover during development

## Example 7: Customization

### Changing API URL
Edit `frontend/.env`:
```
REACT_APP_API_URL=https://api.mysite.com/todolist/api.php
```

### Changing Default Title Format
Edit `frontend/src/App.js`, find the createTodolist function:
```javascript
body: JSON.stringify({
  title: `Tasks - ${new Date().toLocaleDateString('en-US', { 
    weekday: 'long', 
    month: 'long', 
    day: 'numeric' 
  })}`
})
```

Now titles will be like: "Tasks - Monday, December 28"

### Adding More Fields to Items
1. Update database schema in `backend/schema.sql`:
```sql
ALTER TABLE todo_items ADD COLUMN priority VARCHAR(10) DEFAULT 'medium';
```

2. Update API in `backend/api.php` to handle the new field
3. Update frontend in `frontend/src/App.js` to show and edit priority

## Best Practices

1. **Keep titles descriptive** - Make it easy to identify lists at a glance
2. **Create separate lists** - Don't mix personal and work tasks
3. **Check off completed items** - It's satisfying and helps track progress
4. **Review regularly** - Look at your lists daily to stay organized
5. **Archive old lists** - Delete or export completed lists periodically

## Tips and Tricks

- **Keyboard shortcuts**: Press Enter in the add item popup to quickly add tasks
- **Quick edit**: Single click on title to edit instead of double-clicking
- **Mobile use**: The app is fully responsive - use it on your phone!
- **Backup**: The hash IDs allow you to bookmark important lists
- **Batch add**: Add multiple items quickly by repeatedly using the popup

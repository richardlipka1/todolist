# User Interface Guide

## Main Screen

The main screen is the entry point of the application.

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│                   TodoList Manager                      │
│                                                         │
│                    ┌──────────┐                         │
│                    │   ADD    │                         │
│                    └──────────┘                         │
│                                                         │
│                                                         │
│              Existing TodoLists                         │
│    ┌─────────────────────────────────────────────┐     │
│    │ Shopping List (ID: a1b2c3d4...)             │     │
│    ├─────────────────────────────────────────────┤     │
│    │ Work Tasks (ID: e5f6g7h8...)                │     │
│    ├─────────────────────────────────────────────┤     │
│    │ Home Projects (ID: i9j0k1l2...)             │     │
│    └─────────────────────────────────────────────┘     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Features:
- **Large ADD button**: Click to create a new todolist
- **List of existing todolists**: Click any list to open it
- **Hash IDs shown**: Each list displays a truncated hash ID

## TodoList View

When you open or create a todolist, you see:

```
┌─────────────────────────────────────────────────────────┐
│  ┌──────┐     Shopping List                ┌─────────┐ │
│  │ Back │     ID: a1b2c3d4e5f6g7h8...      │ADD Item │ │
│  └──────┘                                   └─────────┘ │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ □ Buy milk                                      │   │
│  ├─────────────────────────────────────────────────┤   │
│  │ ☑ Buy eggs                                      │   │
│  ├─────────────────────────────────────────────────┤   │
│  │ □ Buy bread                                     │   │
│  ├─────────────────────────────────────────────────┤   │
│  │ □ Buy coffee                                    │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Features:
- **Back button**: Return to main screen
- **Editable title**: Click to edit the todolist name
- **Hash ID display**: Shows the unique identifier
- **ADD Item button**: Opens popup to add new tasks
- **Item list**: Shows all tasks with checkboxes
- **Checked items**: Display with strikethrough and gray color

## Add Item Popup

When you click "ADD Item", a modal appears:

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│     ┌─────────────────────────────────────────┐        │
│     │  Add New Task                        × │        │
│     ├─────────────────────────────────────────┤        │
│     │                                         │        │
│     │  ┌───────────────────────────────────┐ │        │
│     │  │ Enter task description...         │ │        │
│     │  └───────────────────────────────────┘ │        │
│     │                                         │        │
│     │                  ┌────────┐ ┌────────┐ │        │
│     │                  │ Cancel │ │Add Task│ │        │
│     │                  └────────┘ └────────┘ │        │
│     └─────────────────────────────────────────┘        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Features:
- **Modal overlay**: Darkens the background
- **Text input**: Enter your task description
- **Close button (×)**: Dismiss the popup
- **Cancel button**: Close without adding
- **Add Task button**: Save the new task
- **Enter key**: Press Enter to quickly add the task

## Empty TodoList

When a todolist has no items yet:

```
┌─────────────────────────────────────────────────────────┐
│  ┌──────┐     New Todo List                ┌─────────┐ │
│  │ Back │     ID: m3n4o5p6q7r8s9t0...      │ADD Item │ │
│  └──────┘                                   └─────────┘ │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │                                                 │   │
│  │    No items yet. Click "ADD Item" to create    │   │
│  │           your first task.                      │   │
│  │                                                 │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

## Task States

### Uncompleted Task
```
┌─────────────────────────────────────────┐
│ □ Buy groceries                         │
└─────────────────────────────────────────┘
```
- Empty checkbox
- Normal text color (black)
- No strikethrough

### Completed Task
```
┌─────────────────────────────────────────┐
│ ☑ Buy groceries                         │
└─────────────────────────────────────────┘
```
- Checked checkbox (green background)
- Gray text color
- Strikethrough text

## Editing Title

### Normal State
```
    Shopping List
    ID: a1b2c3d4e5f6g7h8...
```
Click on the title to edit

### Edit Mode
```
    ┌─────────────────────────┐
    │ Shopping List           │
    └─────────────────────────┘
    ID: a1b2c3d4e5f6g7h8...
```
- Title becomes an input field
- Cursor appears for editing
- Press Enter or click away to save

## Responsive Design

### Desktop View
- Wide layout with centered content
- Large buttons and comfortable spacing
- Modal appears in center of screen

### Tablet View
- Slightly narrower layout
- Touch-friendly button sizes
- Optimized modal size

### Mobile View
```
┌─────────────────┐
│ TodoList Manager│
│                 │
│   ┌─────────┐   │
│   │   ADD   │   │
│   └─────────┘   │
│                 │
│ Shopping List   │
│ (ID: a1b2...)   │
│                 │
│ Work Tasks      │
│ (ID: e5f6...)   │
│                 │
└─────────────────┘
```
- Single column layout
- Full-width buttons
- Stacked elements
- Easy thumb navigation

## Color Scheme

The application uses Bootstrap's default color scheme:

- **Primary (Blue)**: Main actions, ADD button
- **Success (Green)**: ADD Item button, checked checkboxes
- **Secondary (Gray)**: Back button, Cancel button
- **Muted (Light Gray)**: Completed tasks, help text
- **Dark**: Text color
- **Light**: Backgrounds, borders

## Interactive Elements

### Hover Effects
- Buttons: Slightly darker shade on hover
- List items: Light gray background on hover
- Title: Light background when hoverable

### Click Effects
- Buttons: Pressed state with darker color
- Checkboxes: Smooth transition to checked state
- List items: No special effect (reserved for checkboxes)

## Accessibility Features

- **Keyboard navigation**: Tab through all interactive elements
- **Enter key shortcuts**: Add tasks without using mouse
- **Clear visual feedback**: Hover states, focus states
- **Color contrast**: WCAG AA compliant
- **Semantic HTML**: Proper heading structure
- **ARIA labels**: Could be added for screen readers (enhancement)

## Browser Compatibility

Tested and working on:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

- Fast initial load (<2 seconds)
- Instant UI updates (optimistic rendering)
- No page refreshes needed
- Minimal API calls
- Efficient rendering with React

## Future UI Enhancements (Ideas)

1. **Drag and drop**: Reorder tasks
2. **Animations**: Smooth transitions when adding/removing items
3. **Themes**: Dark mode, custom color schemes
4. **Icons**: Add icons to tasks based on type
5. **Due dates**: Visual calendar integration
6. **Progress bar**: Show completion percentage
7. **Search/Filter**: Find tasks across multiple lists
8. **Bulk actions**: Select multiple items to check/uncheck

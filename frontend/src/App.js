import React, { useState, useEffect } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import './App.css';

const API_URL = 'http://localhost/todolist/backend/api.php';

function App() {
  const [currentView, setCurrentView] = useState('main');
  const [currentTodolist, setCurrentTodolist] = useState(null);
  const [todolists, setTodolists] = useState([]);
  const [items, setItems] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [newTask, setNewTask] = useState('');
  const [editingTitle, setEditingTitle] = useState(false);
  const [title, setTitle] = useState('');

  useEffect(() => {
    if (currentView === 'main') {
      loadTodolists();
    }
  }, [currentView]);

  const loadTodolists = async () => {
    try {
      const response = await fetch(`${API_URL}?path=todolists`);
      const data = await response.json();
      setTodolists(data.todolists || []);
    } catch (error) {
      console.error('Error loading todolists:', error);
    }
  };

  const createTodolist = async () => {
    try {
      const response = await fetch(`${API_URL}?path=create`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          title: `Todo List ${new Date().toLocaleDateString()}`
        })
      });
      const data = await response.json();
      if (data.success) {
        setCurrentTodolist({
          hash_id: data.hash_id,
          title: data.title
        });
        setTitle(data.title);
        setItems([]);
        setCurrentView('todolist');
      }
    } catch (error) {
      console.error('Error creating todolist:', error);
    }
  };

  const loadTodolist = async (hashId) => {
    try {
      const response = await fetch(`${API_URL}?path=todolist/${hashId}`);
      const data = await response.json();
      if (data.todolist) {
        setCurrentTodolist(data.todolist);
        setTitle(data.todolist.title);
        setItems(data.items || []);
        setCurrentView('todolist');
      }
    } catch (error) {
      console.error('Error loading todolist:', error);
    }
  };

  const addItem = async () => {
    if (!newTask.trim()) return;

    try {
      const response = await fetch(`${API_URL}?path=add-item`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          hash_id: currentTodolist.hash_id,
          task: newTask
        })
      });
      const data = await response.json();
      if (data.success) {
        setItems([...items, {
          id: data.item_id,
          task: data.task,
          is_done: data.is_done
        }]);
        setNewTask('');
        setShowModal(false);
      }
    } catch (error) {
      console.error('Error adding item:', error);
    }
  };

  const toggleItem = async (itemId, isDone) => {
    try {
      const response = await fetch(`${API_URL}?path=toggle-item`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          item_id: itemId,
          is_done: !isDone
        })
      });
      const data = await response.json();
      if (data.success) {
        setItems(items.map(item => 
          item.id === itemId ? { ...item, is_done: !isDone } : item
        ));
      }
    } catch (error) {
      console.error('Error toggling item:', error);
    }
  };

  const updateTitle = async (newTitle) => {
    try {
      const response = await fetch(`${API_URL}?path=update-title`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          hash_id: currentTodolist.hash_id,
          title: newTitle
        })
      });
      const data = await response.json();
      if (data.success) {
        setCurrentTodolist({ ...currentTodolist, title: newTitle });
        setEditingTitle(false);
      }
    } catch (error) {
      console.error('Error updating title:', error);
    }
  };

  const handleTitleEdit = () => {
    if (editingTitle && title !== currentTodolist.title) {
      updateTitle(title);
    } else {
      setEditingTitle(!editingTitle);
    }
  };

  return (
    <div className="container mt-5">
      {currentView === 'main' ? (
        <div className="main-screen">
          <div className="text-center">
            <h1 className="mb-4">TodoList Manager</h1>
            <button 
              className="btn btn-primary btn-lg"
              onClick={createTodolist}
            >
              ADD
            </button>
          </div>
          
          {todolists.length > 0 && (
            <div className="mt-5">
              <h3>Existing TodoLists</h3>
              <div className="list-group">
                {todolists.map((list) => (
                  <button
                    key={list.hash_id}
                    className="list-group-item list-group-item-action"
                    onClick={() => loadTodolist(list.hash_id)}
                  >
                    {list.title}
                    <small className="text-muted ms-2">
                      (ID: {list.hash_id.substring(0, 8)}...)
                    </small>
                  </button>
                ))}
              </div>
            </div>
          )}
        </div>
      ) : (
        <div className="todolist-screen">
          <div className="d-flex justify-content-between align-items-center mb-4">
            <button 
              className="btn btn-secondary"
              onClick={() => setCurrentView('main')}
            >
              ← Back
            </button>
            <div className="flex-grow-1 mx-3">
              {editingTitle ? (
                <input
                  type="text"
                  className="form-control form-control-lg"
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  onBlur={handleTitleEdit}
                  onKeyPress={(e) => e.key === 'Enter' && handleTitleEdit()}
                  autoFocus
                />
              ) : (
                <h2 
                  className="mb-0 editable-title"
                  onClick={() => setEditingTitle(true)}
                  style={{ cursor: 'pointer' }}
                >
                  {currentTodolist?.title}
                </h2>
              )}
              <small className="text-muted">
                ID: {currentTodolist?.hash_id}
              </small>
            </div>
            <button 
              className="btn btn-success"
              onClick={() => setShowModal(true)}
            >
              ADD Item
            </button>
          </div>

          <div className="card">
            <div className="card-body">
              {items.length === 0 ? (
                <p className="text-muted text-center">No items yet. Click "ADD Item" to create your first task.</p>
              ) : (
                <ul className="list-group list-group-flush">
                  {items.map((item) => (
                    <li 
                      key={item.id} 
                      className="list-group-item d-flex align-items-center"
                    >
                      <input
                        type="checkbox"
                        className="form-check-input me-3"
                        checked={item.is_done}
                        onChange={() => toggleItem(item.id, item.is_done)}
                      />
                      <span 
                        style={{ 
                          textDecoration: item.is_done ? 'line-through' : 'none',
                          color: item.is_done ? '#6c757d' : 'inherit'
                        }}
                      >
                        {item.task}
                      </span>
                    </li>
                  ))}
                </ul>
              )}
            </div>
          </div>
        </div>
      )}

      {/* Modal for adding new item */}
      {showModal && (
        <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Add New Task</h5>
                <button 
                  type="button" 
                  className="btn-close" 
                  onClick={() => {
                    setShowModal(false);
                    setNewTask('');
                  }}
                ></button>
              </div>
              <div className="modal-body">
                <input
                  type="text"
                  className="form-control"
                  placeholder="Enter task description"
                  value={newTask}
                  onChange={(e) => setNewTask(e.target.value)}
                  onKeyPress={(e) => e.key === 'Enter' && addItem()}
                  autoFocus
                />
              </div>
              <div className="modal-footer">
                <button 
                  type="button" 
                  className="btn btn-secondary"
                  onClick={() => {
                    setShowModal(false);
                    setNewTask('');
                  }}
                >
                  Cancel
                </button>
                <button 
                  type="button" 
                  className="btn btn-primary"
                  onClick={addItem}
                >
                  Add Task
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default App;

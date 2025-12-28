#!/bin/bash

echo "TodoList Application Setup"
echo "=========================="
echo ""

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo "Error: MySQL is not installed. Please install MySQL first."
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "Error: Node.js is not installed. Please install Node.js first."
    exit 1
fi

# Setup database
echo "Setting up database..."
echo "Please enter your MySQL root password when prompted:"
mysql -u root -p < backend/schema.sql

if [ $? -eq 0 ]; then
    echo "✓ Database created successfully"
else
    echo "✗ Database setup failed"
    exit 1
fi

# Install frontend dependencies
echo ""
echo "Installing frontend dependencies..."
cd frontend
npm install

if [ $? -eq 0 ]; then
    echo "✓ Frontend dependencies installed successfully"
else
    echo "✗ Frontend setup failed"
    exit 1
fi

echo ""
echo "=========================="
echo "Setup completed successfully!"
echo ""
echo "To start the application:"
echo "1. Make sure your PHP server is running and serving the backend directory"
echo "2. Run 'cd frontend && npm start' to start the frontend development server"
echo ""
echo "The frontend will be available at http://localhost:3000"
echo "The backend API should be accessible at http://localhost/todolist/backend/api.php"
echo ""

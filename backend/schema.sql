-- Create database for todolist application
CREATE DATABASE IF NOT EXISTS todolist_db;
USE todolist_db;

-- Table for storing todolists
CREATE TABLE IF NOT EXISTS todolists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hash_id VARCHAR(32) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for storing todo items
CREATE TABLE IF NOT EXISTS todo_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    todolist_hash VARCHAR(32) NOT NULL,
    task TEXT NOT NULL,
    is_done BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (todolist_hash) REFERENCES todolists(hash_id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_todolist_hash ON todo_items(todolist_hash);
CREATE INDEX idx_hash_id ON todolists(hash_id);

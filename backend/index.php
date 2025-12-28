<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1 { color: #333; }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .endpoint {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <h1>TodoList API</h1>
    <p>Welcome to the TodoList API. This API provides endpoints for managing todolists and their items.</p>
    
    <h2>Available Endpoints</h2>
    
    <div class="endpoint">
        <strong>POST</strong> <code>api.php?path=create</code><br>
        Create a new todolist with a generated hash ID
    </div>
    
    <div class="endpoint">
        <strong>GET</strong> <code>api.php?path=todolist/{hash_id}</code><br>
        Get a specific todolist with all its items
    </div>
    
    <div class="endpoint">
        <strong>GET</strong> <code>api.php?path=todolists</code><br>
        Get all todolists
    </div>
    
    <div class="endpoint">
        <strong>POST</strong> <code>api.php?path=add-item</code><br>
        Add a new item to a todolist
    </div>
    
    <div class="endpoint">
        <strong>POST</strong> <code>api.php?path=toggle-item</code><br>
        Toggle the done status of an item
    </div>
    
    <div class="endpoint">
        <strong>POST</strong> <code>api.php?path=update-title</code><br>
        Update the title of a todolist
    </div>
    
    <h2>Database Status</h2>
    <?php
    require_once 'config.php';
    
    try {
        $conn = getDBConnection();
        echo '<p style="color: green;">✓ Database connection successful</p>';
        
        // Check if tables exist
        $result = $conn->query("SHOW TABLES LIKE 'todolists'");
        if ($result->num_rows > 0) {
            echo '<p style="color: green;">✓ Tables are set up correctly</p>';
            
            // Count todolists
            $result = $conn->query("SELECT COUNT(*) as count FROM todolists");
            $row = $result->fetch_assoc();
            echo '<p>Total todolists: ' . $row['count'] . '</p>';
        } else {
            echo '<p style="color: orange;">⚠ Tables not found. Please run schema.sql to create the database tables.</p>';
        }
        
        $conn->close();
    } catch (Exception $e) {
        echo '<p style="color: red;">✗ Database connection failed: ' . $e->getMessage() . '</p>';
    }
    ?>
    
    <p><a href="../frontend/dist/index.html">Go to Frontend Application</a></p>
</body>
</html>

# Security Features

This document outlines the security measures implemented in the TodoList application.

## Backend Security (PHP)

### 1. Input Validation and Sanitization

All user inputs are validated and sanitized before being processed:

```php
// Sanitize input string
function sanitizeInput($input, $maxLength = 255) {
    if (!is_string($input)) {
        return '';
    }
    $input = trim($input);
    $input = substr($input, 0, $maxLength);
    return $input;
}
```

**Applied to:**
- Todo list titles (max 255 characters)
- Task descriptions (max 1000 characters)

### 2. Hash ID Validation

Hash IDs are validated to ensure they match the expected format (32-character MD5 hex):

```php
function isValidHash($hash) {
    return is_string($hash) && preg_match('/^[a-f0-9]{32}$/i', $hash);
}
```

**Prevents:**
- SQL injection through malformed hash IDs
- Path traversal attacks
- Invalid data types

### 3. SQL Injection Prevention

All database queries use prepared statements with parameter binding:

```php
$stmt = $conn->prepare("INSERT INTO todolists (hash_id, title) VALUES (?, ?)");
$stmt->bind_param("ss", $hash, $title);
$stmt->execute();
```

**Benefits:**
- User input is never directly concatenated into SQL
- Parameters are properly escaped by the database driver
- Protection against all forms of SQL injection

### 4. Integer Validation

Item IDs are validated as positive integers:

```php
$item_id = filter_var($data['item_id'], FILTER_VALIDATE_INT);
if ($item_id === false || $item_id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid item ID']);
    break;
}
```

### 5. CORS Configuration

CORS headers are properly configured to allow cross-origin requests:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

**Note:** In production, replace `*` with specific allowed origins.

### 6. Error Handling

Proper HTTP status codes are returned:
- `200` - Success
- `400` - Bad Request (validation errors)
- `404` - Not Found (todolist not found)
- `405` - Method Not Allowed
- `500` - Internal Server Error

Generic error messages prevent information disclosure:

```php
echo json_encode(['error' => 'Failed to create todolist']);
// Does NOT reveal: "Duplicate key violation on column hash_id"
```

### 7. Database Connection Security

- Connection uses environment variables (supports Docker)
- UTF8MB4 encoding to prevent character encoding attacks
- Connection errors don't expose credentials

## Frontend Security (React)

### 1. XSS Prevention

React automatically escapes all output by default, preventing XSS attacks:

```jsx
<span>{item.task}</span>  // Automatically escaped
```

### 2. Input Validation

Client-side validation prevents empty tasks:

```javascript
if (!newTask.trim()) return;
```

### 3. Environment Variables

Sensitive configuration uses environment variables:

```javascript
const API_URL = process.env.REACT_APP_API_URL || 'http://localhost/todolist/backend/api.php';
```

## Database Security

### 1. Foreign Key Constraints

```sql
FOREIGN KEY (todolist_hash) REFERENCES todolists(hash_id) ON DELETE CASCADE
```

**Benefits:**
- Referential integrity
- Automatic cleanup of orphaned items
- Prevents invalid references

### 2. Indexes

```sql
CREATE INDEX idx_todolist_hash ON todo_items(todolist_hash);
CREATE INDEX idx_hash_id ON todolists(hash_id);
```

**Benefits:**
- Faster query performance
- Prevents denial of service through slow queries

### 3. Data Type Constraints

- `hash_id VARCHAR(32)` - Fixed length prevents buffer overflow
- `is_done BOOLEAN` - Type safety
- `TIMESTAMP` - Proper date handling

## Security Recommendations for Production

### High Priority

1. **Implement Authentication**
   ```php
   // Add session-based or JWT authentication
   if (!isAuthenticated()) {
       http_response_code(401);
       exit();
   }
   ```

2. **Rate Limiting**
   ```php
   // Prevent abuse and DoS attacks
   if (isRateLimited($_SERVER['REMOTE_ADDR'])) {
       http_response_code(429);
       exit();
   }
   ```

3. **HTTPS Only**
   - Force HTTPS in production
   - Use HSTS headers
   ```php
   header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
   ```

4. **Restrict CORS**
   ```php
   $allowed_origins = ['https://yourdomain.com'];
   if (in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
       header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
   }
   ```

5. **Content Security Policy**
   ```php
   header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");
   ```

### Medium Priority

6. **CSRF Protection**
   - Implement CSRF tokens for state-changing operations
   - Verify tokens on POST/PUT/DELETE requests

7. **Input Length Limits**
   - Already implemented (255 for titles, 1000 for tasks)
   - Consider additional limits based on requirements

8. **Logging and Monitoring**
   - Log all failed authentication attempts
   - Monitor for suspicious patterns
   - Alert on unusual activity

9. **Database User Privileges**
   ```sql
   -- Create restricted user for application
   CREATE USER 'todolist_app'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON todolist_db.* TO 'todolist_app'@'localhost';
   ```

10. **Backup Strategy**
    - Regular automated backups
    - Test restore procedures
    - Secure backup storage

### Low Priority

11. **API Versioning**
    ```php
    if ($version !== 'v1') {
        http_response_code(400);
        exit();
    }
    ```

12. **Request Size Limits**
    ```apache
    # In .htaccess or Apache config
    LimitRequestBody 10240  # 10KB limit
    ```

13. **Session Security**
    ```php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
    ```

## Known Limitations

### Current Version

1. **No Authentication**
   - Anyone with the hash ID can access a todolist
   - No user accounts or ownership

2. **Public API**
   - All endpoints are publicly accessible
   - No access control

3. **No Encryption**
   - Data stored in plain text
   - Suitable for non-sensitive data only

4. **CORS Wide Open**
   - Currently allows all origins
   - Should be restricted in production

### Future Enhancements

1. Add user authentication and authorization
2. Implement per-user todolists
3. Add sharing permissions
4. Encrypt sensitive data
5. Add audit logging
6. Implement 2FA for users
7. Add webhook support for integrations

## Testing Security

### Manual Testing

1. **SQL Injection Test**
   ```bash
   curl -X POST http://localhost:8080/api.php?path=add-item \
     -H "Content-Type: application/json" \
     -d '{"hash_id":"test","task":"'; DROP TABLE todo_items;--"}'
   ```
   Expected: Error (invalid hash) or safe insertion (escaped)

2. **XSS Test**
   Add a task with HTML/JavaScript:
   ```
   <script>alert('XSS')</script>
   ```
   Expected: Displayed as plain text, not executed

3. **Hash Validation Test**
   ```bash
   curl http://localhost:8080/api.php?path=todolist/invalid-hash
   ```
   Expected: 400 Bad Request

### Automated Testing

Consider adding:
- PHPUnit tests for validation functions
- OWASP ZAP for vulnerability scanning
- Burp Suite for penetration testing
- npm audit for frontend dependencies

## Reporting Security Issues

If you discover a security vulnerability:

1. **Do not** create a public GitHub issue
2. Email security concerns to the repository owner
3. Include detailed steps to reproduce
4. Allow time for a fix before public disclosure

## Compliance Considerations

This application:
- ✓ Uses prepared statements (OWASP Top 10 #1)
- ✓ Validates all inputs (OWASP Top 10 #3)
- ✓ Uses secure defaults where possible
- ✗ No authentication (add for sensitive data)
- ✗ No encryption (add for compliance requirements)

For GDPR/HIPAA/PCI compliance, additional measures are required.

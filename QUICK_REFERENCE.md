# 🚀 Portfolio Project - Quick Reference Guide

## 📋 File Locations Quick Reference

```
C:\xampp\htdocs\portfolio_project\
├── api/
│   ├── database.php
│   ├── profile_api.php
│   ├── skills_api.php
│   ├── projects_api.php
│   ├── hobbies_api.php
│   └── contacts_api.php
├── class/
│   ├── Profile.php
│   ├── Skill.php
│   ├── Project.php
│   ├── Hobby.php
│   └── Contact.php
└── frontend/
    ├── index.php
    ├── portfolio.js
    └── style.css
```

---

## 🔗 Essential URLs

| Resource | URL |
|----------|-----|
| **phpMyAdmin** | http://localhost/phpmyadmin |
| **Application** | http://localhost/portfolio_project/frontend/index.php |
| **Profile API** | http://localhost/portfolio_project/api/profile_api.php |
| **Skills API** | http://localhost/portfolio_project/api/skills_api.php |
| **Projects API** | http://localhost/portfolio_project/api/projects_api.php |
| **Hobbies API** | http://localhost/portfolio_project/api/hobbies_api.php |
| **Contacts API** | http://localhost/portfolio_project/api/contacts_api.php |

---

## 🗄️ Database Credentials

```php
// In api/database.php
private $host = "localhost";
private $db_name = "portfolio_db";
private $username = "root";
private $password = ""; // Empty for XAMPP
```

---

## 🎯 API Endpoints Cheat Sheet

### Profile API
```bash
# Get all profiles
GET http://localhost/portfolio_project/api/profile_api.php

# Get single profile
GET http://localhost/portfolio_project/api/profile_api.php?id=1

# Get profile with stats
GET http://localhost/portfolio_project/api/profile_api.php?action=stats&id=1

# Create profile
POST http://localhost/portfolio_project/api/profile_api.php
Body: JSON with full_name, email, etc.

# Update profile
PUT http://localhost/portfolio_project/api/profile_api.php
Body: JSON with id, full_name, email, etc.

# Delete profile
DELETE http://localhost/portfolio_project/api/profile_api.php?id=1
```

### Skills API
```bash
# Get all skills
GET http://localhost/portfolio_project/api/skills_api.php

# Get by category
GET http://localhost/portfolio_project/api/skills_api.php?action=by_category

# Get by proficiency
GET http://localhost/portfolio_project/api/skills_api.php?action=by_proficiency&level=Expert

# Get statistics
GET http://localhost/portfolio_project/api/skills_api.php?action=statistics

# Create skill
POST http://localhost/portfolio_project/api/skills_api.php
Body: {skill_name, category, proficiency_level, years_of_experience}

# Update skill
PUT http://localhost/portfolio_project/api/skills_api.php
Body: {id, skill_name, category, proficiency_level, years_of_experience}

# Delete skill
DELETE http://localhost/portfolio_project/api/skills_api.php?id=1
```

### Projects API
```bash
# Get all projects
GET http://localhost/portfolio_project/api/projects_api.php

# Get featured projects
GET http://localhost/portfolio_project/api/projects_api.php?action=featured

# Get by status
GET http://localhost/portfolio_project/api/projects_api.php?action=by_status&status=Completed

# Search by technology
GET http://localhost/portfolio_project/api/projects_api.php?action=search&technology=PHP

# Get statistics
GET http://localhost/portfolio_project/api/projects_api.php?action=statistics

# Get with duration
GET http://localhost/portfolio_project/api/projects_api.php?action=with_duration

# Create project
POST http://localhost/portfolio_project/api/projects_api.php
Body: {project_title, description, technologies_used, status, featured}

# Update project
PUT http://localhost/portfolio_project/api/projects_api.php
Body: {id, project_title, description, ...}

# Delete project
DELETE http://localhost/portfolio_project/api/projects_api.php?id=1
```

### Hobbies API
```bash
# Get all hobbies
GET http://localhost/portfolio_project/api/hobbies_api.php

# Search hobbies
GET http://localhost/portfolio_project/api/hobbies_api.php?action=search&keyword=coding

# Create hobby
POST http://localhost/portfolio_project/api/hobbies_api.php
Body: {hobby_name, description, icon_class}

# Update hobby
PUT http://localhost/portfolio_project/api/hobbies_api.php
Body: {id, hobby_name, description, icon_class}

# Delete hobby
DELETE http://localhost/portfolio_project/api/hobbies_api.php?id=1
```

### Contacts API
```bash
# Get all contacts
GET http://localhost/portfolio_project/api/contacts_api.php

# Get by status
GET http://localhost/portfolio_project/api/contacts_api.php?action=by_status&status=New

# Get recent (last 30 days)
GET http://localhost/portfolio_project/api/contacts_api.php?action=recent&days=30

# Get statistics
GET http://localhost/portfolio_project/api/contacts_api.php?action=statistics

# Search contacts
GET http://localhost/portfolio_project/api/contacts_api.php?action=search&keyword=maria

# Update status only
PUT http://localhost/portfolio_project/api/contacts_api.php?action=update_status
Body: {id, status}

# Delete contact
DELETE http://localhost/portfolio_project/api/contacts_api.php?id=1
```

---

## 🔧 Common Code Snippets

### PHP: Basic API Structure
```php
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'database.php';
require_once '../class/YourClass.php';

$database = new Database();
$db = $database->getConnection();
$object = new YourClass($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': handleGet($object); break;
    case 'POST': handlePost($object); break;
    case 'PUT': handlePut($object); break;
    case 'DELETE': handleDelete($object); break;
}
?>
```

### PHP: Prepared Statement Pattern
```php
$query = "SELECT * FROM table WHERE id = :id";
$stmt = $this->conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetch(PDO::FETCH_ASSOC);
```

### JavaScript: Fetch API Pattern
```javascript
// GET Request
const response = await fetch(`${API_BASE_URL}/endpoint.php`);
const data = await response.json();

// POST Request
const response = await fetch(`${API_BASE_URL}/endpoint.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(dataObject)
});

// PUT Request
const response = await fetch(`${API_BASE_URL}/endpoint.php`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(dataObject)
});

// DELETE Request
const response = await fetch(`${API_BASE_URL}/endpoint.php?id=${id}`, {
    method: 'DELETE'
});
```

---

## 🐛 Quick Debugging Commands

### Check PHP Syntax
```bash
php -l filename.php
```

### Enable PHP Error Display (Development Only)
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check MySQL Connection
```php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=portfolio_db", "root", "");
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
```

### Browser Console Debugging
```javascript
console.log('Variable value:', variable);
console.error('Error:', error);
console.table(arrayData);
```

---

## 📊 Database Quick Commands

### View All Tables
```sql
SHOW TABLES;
```

### Describe Table Structure
```sql
DESCRIBE profile;
```

### Count Records
```sql
SELECT COUNT(*) FROM skills;
```

### View Recent Records
```sql
SELECT * FROM contacts ORDER BY created_at DESC LIMIT 10;
```

### Clear Table Data
```sql
TRUNCATE TABLE table_name;
```

### Reset Auto Increment
```sql
ALTER TABLE table_name AUTO_INCREMENT = 1;
```

---

## ⚡ Performance Optimization Tips

### Add Index
```sql
CREATE INDEX idx_column_name ON table_name(column_name);
```

### Add FULLTEXT Index
```sql
CREATE FULLTEXT INDEX idx_search ON projects(technologies_used, description);
```

### Optimize Query
```sql
EXPLAIN SELECT * FROM table WHERE condition;
```

---

## 🎨 CSS Color Palette

```css
--primary-color: #3498db;     /* Blue */
--secondary-color: #2c3e50;   /* Dark Gray */
--success-color: #27ae60;     /* Green */
--danger-color: #e74c3c;      /* Red */
--warning-color: #f39c12;     /* Orange */
--info-color: #16a085;        /* Teal */
```

---

## 🔒 Security Checklist

- [ ] Use prepared statements (NEVER string concatenation)
- [ ] Validate all inputs on server-side
- [ ] Sanitize output with `htmlspecialchars()`
- [ ] Use HTTPS in production
- [ ] Implement authentication
- [ ] Add CSRF tokens
- [ ] Set secure password hashing
- [ ] Limit failed login attempts
- [ ] Use environment variables for credentials

---

## 📱 Responsive Breakpoints

```css
/* Mobile */
@media (max-width: 480px) { }

/* Tablet */
@media (max-width: 768px) { }

/* Desktop */
@media (min-width: 1024px) { }
```

---

## 🎯 Common HTTP Status Codes

| Code | Meaning | When to Use |
|------|---------|-------------|
| 200 | OK | Successful GET, PUT, DELETE |
| 201 | Created | Successful POST |
| 400 | Bad Request | Validation error |
| 401 | Unauthorized | Not authenticated |
| 403 | Forbidden | No permission |
| 404 | Not Found | Resource doesn't exist |
| 405 | Method Not Allowed | Wrong HTTP method |
| 500 | Server Error | Unexpected error |

---

## 🧪 Postman Quick Tests

### Test Response Status
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});
```

### Test JSON Response
```javascript
pm.test("Response has data", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.status).to.eql("success");
});
```

### Set Environment Variable
```javascript
pm.environment.set("profile_id", jsonData.data.id);
```

---

## 🚨 Common Errors & Solutions

### Error: "Undefined index"
**Solution:** Check if array key exists before accessing
```php
$value = isset($_GET['key']) ? $_GET['key'] : null;
// OR
$value = $_GET['key'] ?? null;
```

### Error: "Call to undefined function"
**Solution:** Check file is included
```php
require_once 'path/to/file.php';
```

### Error: "Class not found"
**Solution:** Verify class name matches filename
```php
// File: Profile.php
class Profile { }
```

### Error: "Headers already sent"
**Solution:** No output before header() calls
```php
// BAD
echo "something";
header("Content-Type: application/json");

// GOOD
header("Content-Type: application/json");
echo json_encode($data);
```

---

## 💾 Backup Commands

### Export Database
```bash
# In command line
mysqldump -u root -p portfolio_db > backup.sql
```

### Import Database
```bash
# In command line
mysql -u root -p portfolio_db < backup.sql
```

### phpMyAdmin Export
1. Select database
2. Click "Export" tab
3. Select "Quick" or "Custom"
4. Click "Go"

---

## 🎓 Key Concepts Summary

### MVC Pattern
- **Model** (Class files): Business logic & database operations
- **View** (Frontend): User interface & presentation
- **Controller** (API files): Request handling & routing

### RESTful Principles
- Use HTTP methods correctly (GET, POST, PUT, DELETE)
- Stateless communication
- Uniform interface
- Resource-based URLs
- JSON responses

### CRUD Operations
- **C**reate: POST method, INSERT query
- **R**ead: GET method, SELECT query
- **U**pdate: PUT method, UPDATE query
- **D**elete: DELETE method, DELETE query

---

## 📞 Quick Help

### XAMPP Not Starting?
1. Check port conflicts (80, 443, 3306)
2. Run as Administrator
3. Check Windows Services
4. Check Apache/MySQL logs

### Can't Connect to Database?
1. Check MySQL is running
2. Verify credentials
3. Check database exists
4. Test with phpMyAdmin

### API Returns Blank?
1. Check PHP error logs
2. Enable error display
3. Check file paths
4. Verify JSON syntax

---

**Keep this reference handy during development!**

Print or bookmark this page for quick access to essential information.
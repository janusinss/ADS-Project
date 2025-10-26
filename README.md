# 📁 Portfolio Management System - Complete Guide

## 🎯 Project Overview

This is a complete **Online Portfolio Management System** built following the WMSU College of Computing Studies curriculum requirements. The project demonstrates full-stack web development skills using PHP, MySQL, and modern JavaScript.

---

## 🏗️ Architecture

```
portfolio_project/
├── api/
│   ├── database.php           # PDO Database Connection
│   ├── profile_api.php        # Profile CRUD API
│   ├── skills_api.php         # Skills CRUD API
│   ├── projects_api.php       # Projects CRUD API
│   ├── hobbies_api.php        # Hobbies CRUD API
│   └── contacts_api.php       # Contacts CRUD API
├── class/
│   ├── Profile.php            # Profile Business Logic
│   ├── Skill.php              # Skills Business Logic
│   ├── Project.php            # Projects Business Logic
│   ├── Hobby.php              # Hobbies Business Logic
│   └── Contact.php            # Contacts Business Logic
├── frontend/
│   ├── index.php              # Main Application UI
│   ├── portfolio.js           # Frontend JavaScript (Fetch API)
│   └── style.css              # Application Styling

```

---

## 🚀 Installation Instructions

### **PHASE 1: Database Setup**

#### Step 1: Start XAMPP
1. Open **XAMPP Control Panel**
2. Start **Apache** and **MySQL** modules
3. Verify both are running (green indicators)

#### Step 2: Create Database
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** in left sidebar
3. Database name: `portfolio_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

#### Step 3: Import Schema
1. Select `portfolio_db` from left sidebar
2. Click **"SQL"** tab at top
3. Copy the entire SQL schema from your documents
4. Paste into the SQL text area
5. Click **"Go"** button
6. Verify all 5 tables are created: `profile`, `skills`, `projects`, `hobbies`, `contacts`

#### Step 4: Insert Sample Data
Execute the INSERT statements provided in the schema to populate tables with test data.

---

### **PHASE 2: Backend Setup**

#### Step 1: Create Project Directory
```bash
C:\xampp\htdocs\portfolio_project\
```

Create the following folder structure:
- `api/`
- `class/`
- `frontend/`

#### Step 2: Setup API Files

**File: `api/database.php`**
- Copy the Database class code
- Verify credentials:
  - `host = "localhost"`
  - `db_name = "portfolio_db"`
  - `username = "root"`
  - `password = ""` (empty for XAMPP default)

**Files: `api/*_api.php`**
- Copy all 5 API endpoint files:
  - `profile_api.php`
  - `skills_api.php`
  - `projects_api.php`
  - `hobbies_api.php`
  - `contacts_api.php`

#### Step 3: Setup Class Files

**Files: `class/*.php`**
- Copy all 5 class files:
  - `Profile.php`
  - `Skill.php`
  - `Project.php`
  - `Hobby.php`
  - `Contact.php`

---

### **PHASE 3: Frontend Setup**

#### Step 1: Create Frontend Files

**File: `frontend/index.php`**
- Copy the complete HTML structure
- This includes all modals and forms

**File: `frontend/portfolio.js`**
- Copy all JavaScript code
- **IMPORTANT:** Update `API_BASE_URL` if needed:
  ```javascript
  const API_BASE_URL = 'http://localhost/portfolio_project/api';
  ```

**File: `frontend/style.css`**
- Copy all CSS styling code

---

### **PHASE 4: Testing**

#### Access the Application
Open browser and navigate to:
```
http://localhost/portfolio_project/frontend/index.php
```

#### Expected Behavior:
✅ Navigation tabs appear at top  
✅ Profile section loads first  
✅ Data displays from database  
✅ Click "Add" buttons to open modals  
✅ Forms submit successfully  
✅ Toast notifications appear  
✅ Edit/Delete buttons work  

---

## 🧪 Postman API Testing

### Test Profile API

**1. GET All Profiles**
```
Method: GET
URL: http://localhost/portfolio_project/api/profile_api.php
Expected: JSON array of all profiles
```

**2. GET Single Profile**
```
Method: GET
URL: http://localhost/portfolio_project/api/profile_api.php?id=1
Expected: Single profile JSON object
```

**3. POST Create Profile**
```
Method: POST
URL: http://localhost/portfolio_project/api/profile_api.php
Headers: Content-Type: application/json
Body (raw JSON):
{
    "full_name": "Test User",
    "email": "test@example.com",
    "phone": "+63 912 345 6789",
    "address": "Test Address",
    "bio": "Test bio",
    "photo_url": "https://via.placeholder.com/150",
    "linkedin_url": "https://linkedin.com/in/testuser",
    "github_url": "https://github.com/testuser",
    "website_url": "https://testuser.dev",
    "date_of_birth": "1995-01-15"
}
Expected: {"status":"success","message":"Profile created successfully"}
```

**4. PUT Update Profile**
```
Method: PUT
URL: http://localhost/portfolio_project/api/profile_api.php
Headers: Content-Type: application/json
Body (raw JSON):
{
    "id": 1,
    "full_name": "Updated Name",
    "email": "updated@example.com",
    ... (include all fields)
}
Expected: {"status":"success","message":"Profile updated successfully"}
```

**5. DELETE Profile**
```
Method: DELETE
URL: http://localhost/portfolio_project/api/profile_api.php?id=1
Expected: {"status":"success","message":"Profile deleted successfully"}
```

### Repeat Similar Tests for:
- Skills API: `skills_api.php`
- Projects API: `projects_api.php`
- Hobbies API: `hobbies_api.php`
- Contacts API: `contacts_api.php`

---

## 🔍 Advanced SQL Features Demonstrated

### 1. **Profile Class** - JOINs and Aggregations
```sql
-- getProfileWithStats() method
SELECT p.*, 
       COUNT(DISTINCT e.id) AS total_education,
       GROUP_CONCAT(DISTINCT e.degree) AS degrees
FROM profile p
LEFT JOIN education e ON p.id = e.profile_id
GROUP BY p.id
```

### 2. **Skills Class** - GROUP BY with HAVING
```sql
-- getSkillsByCategory() method
SELECT category, 
       COUNT(*) AS skill_count,
       AVG(years_of_experience) AS avg_experience
FROM skills
GROUP BY category
HAVING skill_count > 0
```

### 3. **Projects Class** - FULLTEXT Search
```sql
-- searchByTechnology() method
SELECT *, MATCH(technologies_used, description) 
       AGAINST('PHP' IN NATURAL LANGUAGE MODE) AS relevance
FROM projects
WHERE MATCH(technologies_used, description) 
      AGAINST('PHP' IN NATURAL LANGUAGE MODE)
ORDER BY relevance DESC
```

### 4. **Projects Class** - Date Calculations with CASE
```sql
-- getProjectsWithDuration() method
SELECT *,
       DATEDIFF(COALESCE(end_date, CURDATE()), start_date) AS duration_days,
       CASE 
           WHEN DATEDIFF(...) > 180 THEN 'Long Term'
           WHEN DATEDIFF(...) > 90 THEN 'Medium Term'
           ELSE 'Short Term'
       END AS duration_type
FROM projects
```

### 5. **Contacts Class** - Date Filtering
```sql
-- getRecentContacts() method
SELECT *, 
       DATE_FORMAT(created_at, '%M %d, %Y') AS formatted_date,
       DATEDIFF(CURDATE(), DATE(created_at)) AS days_ago
FROM contacts
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
```

---

## 🐛 Troubleshooting

### Database Connection Fails
**Error:** "Database connection failed"  
**Solution:**
1. Verify MySQL is running in XAMPP
2. Check credentials in `api/database.php`
3. Ensure `portfolio_db` exists in phpMyAdmin
4. Check for typos in database name

### CORS Errors
**Error:** "Access-Control-Allow-Origin"  
**Solution:** Already handled in API files with headers:
```php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
```

### 404 Not Found
**Error:** "404 Not Found" when accessing APIs  
**Solution:**
1. Verify file paths match exactly
2. Check `API_BASE_URL` in `portfolio.js`
3. Ensure Apache is running in XAMPP
4. Test URL directly in browser

### Data Not Loading
**Error:** No data displays in frontend  
**Solution:**
1. Open browser Developer Tools (F12)
2. Check Console tab for errors
3. Check Network tab for failed requests
4. Verify database has sample data
5. Test API endpoints directly in browser

### Modal Not Opening
**Error:** Clicking "Add" button does nothing  
**Solution:**
1. Check browser console for JavaScript errors
2. Verify `portfolio.js` is loaded (check Network tab)
3. Ensure modal functions are defined
4. Check for JavaScript syntax errors

---

## 📊 Features Checklist

### Backend (API Layer)
- ✅ PDO Database Connection Class
- ✅ 5 Complete CRUD API Endpoints
- ✅ Prepared Statements (SQL Injection Protection)
- ✅ Proper HTTP Status Codes
- ✅ JSON Response Format
- ✅ Error Handling with try-catch
- ✅ Input Validation

### Business Logic (Class Layer)
- ✅ 5 Complete CRUD Classes
- ✅ Advanced SQL Queries (JOINs, GROUP BY
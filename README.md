# CRM System - Dynamic User Management

A Laravel-based CRM system with dynamic field management, contact relationships, and user merging capabilities following Laravel best practices.

## Features

### Task 1: Basic CRM Features
- **CRUD Operations**: Complete user management (Create, Read, Update, Delete)
- **Standard Fields**: Name, Email, Phone, Gender (radio), Profile Image, Additional File
- **Dynamic Fields**: Add custom fields with labels (e.g., "Birth Date" → stored as "birth_date")
- **AJAX Integration**: All operations without page refresh
- **Advanced Filtering**: Filter by name, email, gender, and dynamic fields
- **File Uploads**: Profile images and additional documents
- **Form Validation**: Request classes for clean validation

### Task 2: Contact Management & Merging
- **Contact Relationships**: Users can have multiple contacts
- **Contact Management**: Add/remove contacts with modal interface
- **User Merging**: Merge two users with master selection
- **Data Preservation**: All dynamic fields preserved during merge
- **Merge History**: Track merged users with modified emails
- **Smart Contact Selection**: Only show available contacts (not already added)

## Database Structure

### 1. Users Table
```sql
- id, name, email, phone, gender, profile_image, additional_file, password
- Indexes: [name, email], gender
```

### 2. User_Details Table (Dynamic Fields)
```sql
- id, user_id, key, label, value
- Foreign Key: user_id → users.id
- Index: [user_id, key]
```

### 3. Contacts Table
```sql
- id, user_id, contact_id, is_merged, merged_into
- Foreign Keys: user_id → users.id, contact_id → users.id
- Indexes: [user_id, is_merged], unique[user_id, contact_id]
```

## Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/MariaDB
- XAMPP/WAMP (recommended)

### Installation Steps

1. **Clone/Download Project**
   ```bash
   cd xampp/htdocs
   # Place project in practicalDynamicForm folder
   ```

2. **Install Dependencies**
   ```bash
   cd practicalDynamicForm
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   - Create database: `practical_dynamic_form`
   - Update `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=practical_dynamic_form
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate:fresh
   ```

6. **Seed Sample Data**
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

7. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

9. **Access Application**
   - URL: `http://localhost:8000`
   - Or via XAMPP: `http://localhost/practicalDynamicForm/public`

## Usage Guide

### User Management
1. **Add User**: Click "Add New User" → Fill form → Add dynamic fields → Save
2. **Edit User**: Click "Edit" → Modify data → Update dynamic fields → Save
3. **View Details**: Click "View" → See complete user profile
4. **Delete User**: Click "Delete" → Confirm deletion

### Dynamic Fields
- **Add Field**: In user form → "Add Dynamic Field" → Enter label & value
- **Field Storage**: Label "Birth Date" → Key "birth_date" in database
- **Filter by Fields**: Use dynamic field dropdown in filters

### Contact Management
1. **Add Contact**: Click "Contacts" → Select user → "Add Contact"
2. **View Contacts**: User details page shows active contacts
3. **Remove Contact**: In contacts modal → Click "Remove"

### User Merging
1. **Initiate Merge**: Click "Merge" on any user
2. **Select Second User**: Choose user to merge with
3. **Choose Master**: Select which user remains primary
4. **Confirm Merge**: All data preserved, secondary user marked as merged

### Filtering & Search
- **Basic Filters**: Name, Email, Gender
- **Dynamic Filters**: Select field → Enter value → Filter
- **Combined Filters**: Use multiple filters together
- **Clear Filters**: Reset all filters

## File Structure
```
app/
├── Http/
│   ├── Controllers/UserController.php
│   └── Requests/
│       ├── StoreUserRequest.php
│       ├── UpdateUserRequest.php
│       └── AddContactRequest.php
└── Models/
    ├── User.php
    ├── UserDetail.php
    └── Contact.php

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2024_01_01_100002_create_user_details_table.php
│   └── 2024_01_01_100003_create_contacts_table.php
└── seeders/UserSeeder.php

resources/views/
├── layouts/app.blade.php
├── partials/modals.blade.php
└── users/
    ├── index.blade.php
    └── show.blade.php

public/js/app.js
routes/web.php
```

## Key Features Implemented

✅ **CRUD with AJAX** - All operations without page refresh  
✅ **Dynamic Fields** - Raw database storage with key/label/value  
✅ **File Uploads** - Profile images and additional files  
✅ **Advanced Filtering** - Including dynamic field filters  
✅ **Contact Management** - Add/remove user contacts  
✅ **User Merging** - Preserve all data during merge  
✅ **Merge History** - Track merged users  
✅ **Responsive Design** - Bootstrap-based UI  
✅ **Data Integrity** - Proper foreign keys and indexing  
✅ **Request Validation** - Form Request classes for clean validation  
✅ **Modular Views** - Layout system with partials  
✅ **Separated JavaScript** - Clean code organization  
✅ **Guarded Models** - Security best practices  

## Sample Data
The seeder creates 3 users with various dynamic fields and contact relationships for testing.

## Technical Notes
- **Dynamic Field Keys**: Spaces replaced with underscores, lowercase
- **Merge Logic**: Secondary user email modified with timestamp
- **File Storage**: Uses Laravel's storage system with public disk
- **AJAX Security**: CSRF tokens on all requests
- **Database Optimization**: Proper indexing for filters
- **Code Organization**: Request classes, layout system, separated JS
- **Security**: Guarded models, proper validation
- **Architecture**: Follows Laravel best practices and conventions

## Code Quality Features
- **Request Classes**: Centralized validation logic
- **Blade Layouts**: Reusable template structure
- **Partial Views**: Modular component system
- **Separated Assets**: JavaScript in dedicated files
- **Model Security**: Guarded attributes instead of fillable
- **Clean Controllers**: Validation moved to request classes
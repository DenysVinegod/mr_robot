# Admin Panel - CRUD Operations Documentation

## Overview
A comprehensive admin panel has been added for superadmin users to manage all system entities through CRUD (Create, Read, Update, Delete) operations.

## Access Control
- **Admin Panel URL**: `/app/views/admin.php`
- **Accessible to**: Superadmin users only
- **Menu Item**: "Адміністрація" appears in the main menu for superadmin users

## Features

### 1. Users Management (Користувачі)
**Tab**: Users (Користувачі)

**Operations**:
- **Create**: Add new users with login and role assignment
- **Read**: View list of all users with their roles
- **Update**: Edit user login and role (edit button opens a modal)
- **Delete**: Remove users from the system

**Fields**:
- Login (username)
- Password (hashed with bcrypt)
- Role (superadmin, reception, master)

### 2. Clients Management (Клієнти)
**Tab**: Clients (Клієнти)

**Operations**:
- **Create**: Add new clients with personal information
- **Read**: View list of all clients
- **Update**: Edit client information
- **Delete**: Remove clients

**Fields**:
- First Name (Ім'я)
- Surname (Прізвище)
- Last Name (По батькові)

### 3. Contacts Management (Контакти)
**Tab**: Contacts (Контакти)

**Operations**:
- **Create**: Add new contacts for clients
- **Read**: View list of all contacts
- **Update**: Edit contact information
- **Delete**: Remove contacts

**Fields**:
- Client ID (link to client)
- Contact Type (phone, email, etc.)
- Contact (actual phone number or email)

### 4. Devices Management (Пристрої)
**Tab**: Devices (Пристрої)

**Operations**:
- **Create**: Add new devices for clients
- **Read**: View list of all devices
- **Update**: Edit device information and description
- **Delete**: Remove devices

**Fields**:
- Client ID (link to client)
- Device Type (laptop, phone, etc.)
- Description (device details/notes)

### 5. Repairs Management (Ремонти)
**Tab**: Repairs (Ремонти)

**Operations**:
- **Read**: View list of all repairs
- **Update**: Edit repair details (via link to repairs.php)
- **Delete**: Remove repair records

**Fields Displayed**:
- Repair ID
- Status
- Client name
- Contact information
- Device name
- Description

## UI Features

### Tabbed Interface
- Easy navigation between different entity types
- All tabs accessible from the admin panel

### Modal Dialogs
- Edit forms appear in modal dialogs
- Smooth user experience without page reloads
- Modal closes by clicking the X button or outside the modal area

### Action Buttons
- **Edit Button** (Green) - Opens edit modal
- **Delete Button** (Red) - Deletes with confirmation

### Data Tables
- All entities displayed in organized tables
- Columns include all relevant information
- Row highlighting on hover

## Database Structure

### Users Table
```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  login VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role_id INT,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

### Roles Table
```sql
CREATE TABLE roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) UNIQUE NOT NULL,
  description VARCHAR(255)
);
```

### Clients Table
```sql
CREATE TABLE clients (
  id INT PRIMARY KEY AUTO_INCREMENT,
  first_name VARCHAR(100),
  surname VARCHAR(100),
  last_name VARCHAR(100)
);
```

### Contacts Table
```sql
CREATE TABLE contacts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_id INT,
  type_id INT,
  contact VARCHAR(255),
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (type_id) REFERENCES contact_types(id)
);
```

### Devices Table
```sql
CREATE TABLE devices (
  id INT PRIMARY KEY AUTO_INCREMENT,
  client_id INT,
  type_id INT,
  description TEXT,
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (type_id) REFERENCES device_types(id)
);
```

## File Structure

### New Files
- `/app/models/users.php` - Users model with CRUD methods
- `/app/controllers/admin.php` - Admin panel controller handling all operations
- `/app/views/admin.php` - Admin panel view with tabs and forms

### Modified Files
- `/app/views/layouts/_main_menu.php` - Added admin link for superadmin
- `/app/models/devices.php` - Added description field support

## Controllers and Models

### Users Model (`/app/models/users.php`)
Methods:
- `save_new_user(array $data)` - Create new user
- `update_user(array $data)` - Update user
- `delete_user(int $id)` - Delete user
- `get_user_by_id(int $id)` - Get user details
- `list_all_users()` - Get all users with roles

### Admin Controller (`/app/controllers/admin.php`)
Handles all POST actions:
- User CRUD: create_user, update_user, delete_user
- Client CRUD: create_client, update_client, delete_client
- Contact CRUD: create_contact, update_contact, delete_contact
- Device CRUD: create_device, update_device, delete_device
- Repair operations: delete_repair

## Security Features
- Role-based access control (only superadmin can access)
- Password hashing with bcrypt
- SQL injection prevention through input validation
- CSRF protection through session validation

## Usage Examples

### Accessing the Admin Panel
1. Login as a superadmin user
2. Click "Адміністрація" in the main menu
3. Select the desired tab

### Creating a New User
1. Go to Users tab
2. Fill in Login, Password, and select Role
3. Click "Створити користувача"

### Editing an Entity
1. Find the entity in the table
2. Click the green "Редагувати" button
3. Update information in the modal
4. Click "Оновити" button

### Deleting an Entity
1. Find the entity in the table
2. Click the red "Видалити" button
3. Confirm deletion when prompted

## Future Enhancements
- Add user profiles and additional user information
- Add sorting and filtering to tables
- Add pagination for large datasets
- Add search functionality
- Add bulk operations
- Add activity logging
- Add backup/export functionality

# 🗂️ Leave Management System (PHP & MySQL)

This is a **Leave Management System** developed as part of a Web Programming project. The system allows employees to apply for leave, while managers and administrators can manage user accounts, view reports, and approve/reject leave requests.

Built using **PHP**, **MySQL**, and **CSS**, the system runs locally on **XAMPP** and provides separate dashboards for Admins, Managers, and Staff.

---

## 🚀 Features

- 👨‍💼 Admin Dashboard
  - Add / update / delete users
  - View all leave reports
- 📋 Manager Dashboard
  - Approve / reject leave applications
  - View team leave records
- 👤 Staff Dashboard
  - Apply for leave
  - View own leave history
  - Edit account information
- 🔐 User Login Handling
- 📊 Simple report listing and status tracking
- 🎨 Custom UI using CSS

---

## 🛠️ Technologies Used

- **PHP** – Backend scripting
- **MySQL** – Database management
- **HTML/CSS** – Frontend styling
- **XAMPP** – Local server environment

---

## 🗃️ File Structure

### 📁 Main Functional Files

| Filename                    | Description                                 |
|----------------------------|---------------------------------------------|
| `AddUser.php`              | Admin: Add new user                         |
| `ManagerDef.php`           | Manager interface and controls              |
| `StaffDef.php`             | Staff dashboard                             |
| `LeaveApplyForm.php`       | Form for staff to apply leave               |
| `ApproveReject.php`        | Approve/reject leave (Manager)              |
| `LeaveReport.php`          | Admin leave report viewer                   |
| `ReportList.php`           | List of leave reports (Admin)               |
| `ReportListStaff.php`      | Staff view of leave history                 |
| `UserLoginHandling.php`    | Handles login authentication                |
| `configuration.php`        | Database connection settings                |
| `createDatabase.php`       | Script to create database                   |
| `create_table.php`         | Script to create required tables            |

### 🎨 Stylesheets

| Filename            | Description                         |
|---------------------|-------------------------------------|
| `AddUser.css`       | Styling for Add User page           |
| `applyleave.css`    | Leave form styles                   |
| `admins.css`        | Admin dashboard styles              |
| `manager.css`       | Manager dashboard styles            |
| `staff.css`         | Staff dashboard styles              |
| `editacc.css`       | Edit profile page styling           |

### 📂 Others

- `insert_new_user.php`, `update_user.php`, `delete_user.php`: Handle CRUD for user accounts  
- `logout.php`: Handles user logout  
- `mainpage.php`: Redirect placeholder or dashboard router  
- `loginform.php`: Login page interface  
- `SelfAccEdit.php`: Staff edit account functionality  

---

## 🖥️ How to Run the Project

### ✅ Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) installed
- PHP and MySQL enabled via XAMPP Control Panel

### 📦 Steps to Run

1. **Copy project folder** into `htdocs` (usually at `C:\xampp\htdocs`):

2. **Start XAMPP**:
- Open **XAMPP Control Panel**
- Start **Apache** and **MySQL**

3. **Create Database**:
- Open browser and go to:  
  `http://localhost/phpmyadmin`
- Create a new database (e.g., `leavemanagement`)
- Import or run `createDatabase.php` and `create_table.php` from your project folder

4. **Access the App**:
- Go to:
  ```
  http://localhost/LeaveManagementSystem/loginform.php
  ```

5. **Login as Admin/Manager/Staff** using test credentials

---

## 📌 Notes

- This project does **not** use Firebase, APIs, or external libraries
- All user data and leave details are stored in **MySQL**
- UI is styled manually using custom **CSS**

---

## 📃 License

This project is created for educational purposes only and is not intended for production use.

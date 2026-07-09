# Livestock Health & Export Tracking System

A web-based system developed to manage livestock health records, vaccination information, and export permit processing. The system helps veterinary officers and export authorities maintain accurate records and generate export permit documents efficiently.

---

##  Project Overview

The Livestock Health & Export Tracking System is designed to digitalize the livestock management process by providing a centralized platform for:

- Registering livestock owners
- Managing animal records
- Recording health inspections
- Tracking vaccinations
- Generating export permits
- Producing reports and statistics

---

##  Features

### Authentication
- Secure Login
- Logout
- Session Management

### Dashboard
- System Overview
- Statistics Cards
- Quick Navigation

### Owner Management
- Add Owner
- View Owners
- Edit Owner
- Delete Owner

### Animal Management
- Register Animals
- Update Animal Information
- Delete Animals
- Search Animals

### Health Records
- Record Animal Health Inspections
- Record Treatments
- Track Health Status

### Vaccination Management
- Add Vaccination Records
- View Vaccination History
- Track Next Vaccination Date

### Export Permit
- Generate Export Permits
- Export Permit Approval
- PDF Generation

### Reports
- Animal Reports
- Health Reports
- Export Reports

---

##  Technologies Used

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript

### Backend
- PHP

### Database
- MySQL

### Development Tools
- XAMPP
- Visual Studio Code
- Git
- GitHub

---

## 📂 Project Structure

```
Livestock-Health-Export-Tracking
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── auth/
├── dashboard/
├── owners/
├── animals/
├── health/
├── exports/
├── reports/
├── includes/
├── database/
│
├── index.php
└── README.md
```

---

## 🗄 Database Tables

- users
- owners
- animals
- health_records
- vaccinations
- export_permits

---

##  User Roles

### Administrator
- Manage all system modules
- Manage users
- Generate reports

### Veterinary Officer
- Register animals
- Record health inspections
- Record vaccinations

### Export Officer
- Review animal records
- Generate export permits
- Print PDF permits

---

##  Security Features

- Password Hashing
- Session Authentication
- Input Validation
- Secure Database Connection
- Role-Based Access Control

---

##  Modules

- Authentication
- Dashboard
- Owner Management
- Animal Management
- Health Records
- Vaccination Management
- Export Permit
- Reports

---

## ⚙ Installation

1. Clone the repository

```bash
git clone https://github.com/yourusername/Livestock-Health-Export-Tracking.git
```

2. Move the project to:

```
C:\xampp\htdocs\
```

3. Start Apache and MySQL from XAMPP.

4. Create a MySQL database named:

```
livestock_db
```

5. Import the SQL file from:

```
database/livestock.sql
```

6. Configure the database connection in:

```
includes/config.php
```

7. Open your browser:

```
http://localhost/Livestock-Health-Export-Tracking
```

---

## 📈 Future Improvements

- QR Code Integration
- Barcode Support
- Email Notifications
- SMS Notifications
- Mobile Application
- Cloud Deployment

---

## 📄 License

This project was developed for academic purposes as a Software Engineering final project.

---

## 👨‍💻 Developer

**Name:** Ibrahim Hassan Ibrahim, Hotho Abdihakin Jama, Safa Ahmad Abdi

**Department:** Software Engineering

**University:** Gollis University Hargeisa

**Academic Year:** 2026

---

## ⭐ GitHub

If you find this project useful, consider giving it a ⭐ on GitHub.
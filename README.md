# 🚀 SIMTO - Employee Attendance & Career Management System

SIMTO is a web-based application built with Laravel for managing employee attendance, face recognition validation, and career/job recruitment.

---

## 📌 Features

### 👨‍💼 Employee

* Check In / Check Out (with photo)
* Face Recognition validation
* Location (GPS) validation with radius
* Daily attendance tracking
* Personal attendance history (My Attendance)
* Download timesheet

### 🧑‍💻 Admin / HR

* Employee Management
* Client (Office Location) Management
* Attendance Monitoring
* Reset Face Descriptor
* Job Vacancy Management (CRUD)
* Applicant Tracking
* Follow-up notes for applicants

### 🌐 Public

* View job vacancies
* Apply for jobs
* Upload CV

---

## 🛠️ Tech Stack

* PHP (Laravel)
* MySQL
* JavaScript (Face API)
* Bootstrap (UI)
* DataTables
* Summernote Editor

---

## ⚙️ Installation Guide

### 1. Clone Repository

```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo
```

---

### 2. Install Dependencies

```bash
composer install
npm install
npm run dev
```

---

### 3. Setup Environment

Copy `.env` file:

```bash
cp .env.example .env
```

Generate key:

```bash
php artisan key:generate
```

---

### 4. Configure Database

Edit `.env`:

```env
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=
```

---

### 5. Run Migration & Seeder

```bash
php artisan migrate --seed
```

---

### 6. Storage Link

```bash
php artisan storage:link
```

---

### 7. Run Server

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## 🔐 Default Login

| Role     | Email                                   | Password |
| -------- | --------------------------------------- | -------- |
| Admin    | [admin@mail.com](mailto:admin@mail.com) | password |
| Employee | [user@mail.com](mailto:user@mail.com)   | password |

---

## 📍 Important Notes

* Face recognition requires:

  * Camera access
  * Browser with WebGL support
* GPS location must be enabled for attendance
* Ensure `.env` is properly configured

---

## 📂 Folder Structure Highlights

```
app/
 ├── Models/
 ├── Http/Controllers/
resources/views/
routes/web.php
database/migrations/
```

---

## 🧪 Troubleshooting

### ❌ Face API Error (WebGL not supported)

* Use Chrome / Edge
* Enable hardware acceleration

### ❌ Image not saved

```bash
php artisan storage:link
```

### ❌ Data not saved

* Check `$fillable` in model
* Check validation rules

---

## 📈 Future Improvements

* Mobile App (Android)
* Push Notification
* Attendance Analytics Dashboard
* Multi-branch support

---

## 👨‍💻 Author

Developed by **Rizki Fadilla**

---

## 📄 License

This project is open-source and free to use.

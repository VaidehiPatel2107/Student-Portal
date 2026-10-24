# StudentHub Portal

A web-based academic management and analytics portal designed for managing student records, course information, event scheduling, attendance tracking, and administrative governance.

---

## 📌 Project Overview

StudentHub is a decoupled web application built with a responsive frontend (Single Page Application via Fetch API) and a modular PHP/MySQL backend. It integrates role-based security, multi-entity dashboard metrics, and security audit logging.

### Key Capabilities
- **Role-Based Access Control (RBAC):** Distinct permissions and views for `Admin` and `Student` roles.
- **RESTful Student Directory:** Asynchronous CRUD operations (GET, POST, PUT, DELETE) using vanilla JavaScript Fetch API.
- **Administrative Dashboard:** Real-time metrics tracking total students, registered accounts, and campus events.
- **Immutable Audit Logging:** Automatic recording of administrative transactions, client IP addresses, and timestamps.
- **Security Protections:** Defenses against SQL Injection (prepared statements) and Cross-Site Scripting (output sanitization).

---

## 👥 User Roles

| Role | Default Username | Default Password | Access Permissions |
| :--- | :--- | :--- | :--- |
| **Admin** | `vaidehi_admin` | `admin123` | Full administrative dashboard, audit log views, event management, and system-wide student controls |
| **Student** | `rahul_student` | `student123` | Student directory view, personal profile actions, attendance/results review (HTTP 403 on privileged routes) |

---

## 📄 Site Structure & Pages

The application is organized into the following core interfaces:

1. **Home (`index.html`):** Asynchronous student directory Single Page Application (SPA).
2. **Login (`login.php`):** Responsive sign-in portal with modern aesthetic and role selection.
3. **About Us (`about-us.html`):** Campus and departmental overview.
4. **Contact Us (`contact-us.html`):** Institutional support details and inquiry channels.
5. **Dashboard (`dashboard.php`):** Centralized analytics hub displaying key metric cards and recent registration feeds.
6. **Announcements & Events (`manage_events.php` / `events.html`):** Campus activity scheduling and notices.
7. **Communication (`communication.html`):** Academic messaging and notifications.
8. **Attendance (`attendance.html`):** Course session attendance monitoring.
9. **University Result (`uni-result.html`):** Semester-wise grade listings and academic performance.
10. **Fees & Finance (`fees.html`):** Fee payment history and ledger overviews.

---

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3 (Bootstrap 5.3.3), JavaScript ES6+ (Fetch API, DOM manipulation)
- **Backend:** PHP 8.2 (RESTful JSON endpoints, session management, prepared statements)
- **Database:** MySQL 8.0 / MariaDB (InnoDB engine, UTF-8 collation)
- **Development Environment:** Apache via XAMPP

---

## 🚀 Installation & Local Setup

### 1. Clone the Repository
```bash
git clone [https://github.com/VaidehiPatel2107/Student-Portal.git](https://github.com/VaidehiPatel2107/Student-Portal.git)

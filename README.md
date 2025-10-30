PROJECT TITLE: Student-Teacher Booking Appointment System (AppointMe)

### 1. PROJECT OVERVIEW

This is a web-based, role-segmented appointment booking system designed for educational institutions to streamline the scheduling of meetings between students and teachers. The system ensures efficiency by providing an interactive platform where students can view real-time teacher availability and teachers can manage and approve requests.

**Key Features & Constraints:**
* **Technologies:** HTML, CSS, JavaScript, PHP, MySQL.
* **Design:** Unique, interactive **Neumorphic UI** (Soft UI) with animations.
* **Backend:** Uses **PHP** for server-side logic and MySQL for the database (replacing Firebase).
* **Security:** Implements **Role-Based Access Control (RBAC)** ensuring strict panel separation (Student cannot access Teacher/Admin, Teacher cannot access Admin).

---

### 2. ROLE-BASED ACCESS CONTROL (RBAC)

The system enforces strict separation of duties across three primary user roles:

| Role | Key Features | Access Restriction |
| :--- | :--- | :--- |
| **Admin** | - Add/Update/Delete Teachers.<br>- Approve pending Student registrations.<br>- System oversight. | Cannot access Teacher or Student Dashboards. |
| **Teacher** | - Set/Manage time slot availability.<br>- Approve/Cancel student appointment requests.<br>- View all messages (purpose). | Cannot access Admin Dashboard. |
| **Student**| - Register (requires Admin approval).<br>- Search Teachers (by name, subject, department).<br>- Book appointments & Send message (purpose). | Cannot access Teacher or Admin Dashboards. |

---

### 3. PROJECT SETUP & EXECUTION STEPS

To run this project, you need a local server environment (like XAMPP, WAMP, or MAMP). These steps assume you are using **XAMPP**.

**A. Environment Setup**

1.  **Install XAMPP:** Download and install XAMPP (or your chosen server package).
2.  **Start Services:** Open the XAMPP Control Panel and click **Start** for both **Apache** and **MySQL**.
3.  **Project Placement:** Place the entire project folder (`appointment_system/`) into the `htdocs` directory of your XAMPP installation (e.g., `C:/xampp/htdocs/`).

**B. Database Configuration (MySQL)**

1.  **Access phpMyAdmin:** Open your web browser and navigate to `http://localhost/phpmyadmin/`.
2.  **Create Database:** Click the **"New"** button on the left sidebar and create a database named: `appointment_db`.
3.  **Execute SQL:** Select the `appointment_db` database, click the **"SQL"** tab, and paste the database creation queries (from the project setup instructions) to create the `users`, `profiles`, `availability`, and `appointments` tables.

    * ***Crucially, this step creates the default Admin user.***

**C. Initial Login & Testing**

1.  **Access Project:** Open your browser and navigate to: `http://localhost/appointment_system/`
2.  **Admin Login (Initial Setup):**
    * **Email:** `admin@app.com`
    * **Password:** `admin123`
3.  **Complete Initial Workflow:**
    * **Admin Task:** Use the Admin panel (`/admin/manage_teachers.php`) to **Add a Teacher**.
    * **Student Task:** Log out, go to `register.php`, and **Register as a new Student**.
    * **Admin Task:** Log back in as Admin and go to (`/admin/approve_students.php`) to **Approve the Student** registration.
    * **Teacher Task:** Log out, log in as the newly created Teacher, and set **Schedule/Availability** slots.
    * **Student Task:** Log out, log in as the Student, **Search for the Teacher**, and **Book an Appointment**.
    * **Teacher Task:** Log in as Teacher to **Approve** the booking.

---

### 4. FILE STRUCTURE

The project follows a modular structure for maintainability and security:

* `/admin/`: Contains all files for the Admin dashboard.
* `/teacher/`: Contains all files for the Teacher dashboard.
* `/student/`: Contains all files for the Student dashboard.
* `/includes/`: Contains reusable PHP files (`db.php`, `header.php`, `auth_check.php`).
* `/assets/`: Stores CSS (`style.css` for Neumorphism) and JavaScript files.
* `login.php`: Universal login page.
* `register.php`: Student-only registration.
* `logout.php`: Session termination script.

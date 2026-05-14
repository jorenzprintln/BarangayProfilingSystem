# Barangay Profiling System

The **Barangay Profiling System** is a web-based application designed to streamline the management of barangay constituents, households, and administrative tasks. Built using **PHP** for the backend and **Bootstrap** for the frontend, this system provides an efficient and user-friendly interface for barangay officials to manage and generate reports required by Local Government Units (LGUs) and other government agencies.

---

## Features

### 1. **Constituent Management**
   - **Add, Delete, Update, and Read (CRUD) Constituents**: Easily manage the records of barangay constituents. Add new constituents, update their information, delete outdated records, and view detailed profiles.

### 2. **Report Generation**
   - **Generate Required Reports**: Automatically generate reports required by LGUs and government agencies. These reports include demographic data, household information, income statistics, and more.

### 3. **Household Management**
   - **View Household List**: Access a comprehensive list of households within the barangay.
   - **Household Details**: View the number of members in each household and other relevant information.

### 4. **Barangay Officials Management**
   - **Assign Administrators**: Assign barangay officials from the constituents.

---

## Technologies Used

- **Backend**: PHP
- **Frontend**: Bootstrap (CSS Library)
- **Database**: MySQL (or any compatible database)

---

## Installation Guide

### Prerequisites
- Web Server (e.g., Apache, Nginx)
- PHP (version 7.0 or higher)
- MySQL Database

### Steps

1. **Clone the Repository**: Paste this into powershell or cmd
   ```bash
   git clone https://github.com/jorenzprintln/BarangayProfilingSystem
   cd barangay-profiling-system
   ```
   
2. **Setup the Database**:

- Create a MySQL database.
- Import the provided SQL file (`re_bps.sql`) into your database.

3. **Configure the Application**:
Open the database.php file and update the database connection details:

    ```php
      const DB_HOST = 'localhost';
      const DB_NAME = 're_bps';
      const DB_USER = 'root';
      const DB_PASS = '';
      const DB_CHARSET = 'utf8mb4';
    ```

4. **Deploy the Application**:
Move the project folder to your web server's root directory (e.g., htdocs for XAMPP or www for WAMP/Laragon).
Access the application via your browser: <http://localhost/barangay_system>.

---

## Usage

1. **Login**:

- Use the default admin credentials to log in (provided in the database or setup instructions).
- Change the default password after the first login.

2. **Manage Constituents**:

- Navigate to the "Constituents" section to add, update, delete, or view constituent records.

3. **Generate Reports**:

- Go to the "Reports" section and select the type of report you need. The system will generate and display the report.

4. **View Households**:

- Access the "Households" section to view a list of households and their details.

5. **Assign Barangay Officials**:

- In the "Barangay Officials" section, assign constituents as barangay officials.

---

## Contributing
We welcome contributions to improve the Barangay Profiling System! If you'd like to contribute, please follow these steps:

- Fork the repository.
- Create a new branch for your feature or bugfix.
- Commit your changes and push to your branch.
- Submit a pull request with a detailed description of your changes.

---

## Contact
For questions, suggestions, or support, please contact:

**Email**: <rayoryanchristian@gmail.com> or <seandavenn@gmail.com> or <jorenzpablo@gmail.com>

Thank you for using the Barangay Profiling System! We hope it helps streamline your barangay management tasks.
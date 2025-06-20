

## Prerequisites

Before you begin, ensure you have the following installed and available in your `PATH`:

1. PHP (version 7.0 or higher)
2. Composer (for PHP dependency management)
3. Python 3 (version 3.8 or higher)
4. MySQL server & client (or compatible MariaDB)

## Installation

1. **Clone the repository**

2. **Import the database**

   Full dataset (with test data)  + single admin user: sql/db_data.sql

   Empty schema + single admin user: sql/db_empty.sql

   Admin user credentials: username: admin1, password: 1234
   Credentials of the admin account can be changed from a mysql client
   For other users in the db_data.sql password is 1234
   

3. **Run the installer script**

   The `install.sh` script will:

   * Check for PHP, Composer, and Python
   * Install PHP dependencies via Composer if not already present
   * Create and activate a Python virtual environment under `pythonSolver/venv`
   * Install PuLP into the venv if missing

   **On Linux/macOS (or WSL):**

   1. Open a terminal and navigate to the project root.
   2. Make the script executable:

      ```bash
      chmod +x install.sh
      ```
   3. Run the script:

      ```bash
      ./install.sh
      ```

   **On Windows (Git Bash):**

   1. Open **Git Bash** and `cd` into the project root.
   2. Ensure the script is executable (Git Bash preserves permissions on clone):

      ```bash
      chmod +x install.sh
      ```
   3. Run the script:

      ```bash
      ./install.sh
      ```

4. **Configuration**

   Update `backend/config/dbConfig.php` with your database credentials if they differ from the defaults:

   ```php
   <?php
   $host     = "localhost";
   $dbname   = "tu_choices";
   $dbuser   = "root";
   $dbpass   = "";
   ```

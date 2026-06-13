# mr. R O B O T
Project for accounting of business activity with a web interface.

## First Launch Instructions

1. Install dependencies
   - Run `composer install` in the project root.

2. Configure PHP
   - Enable the `gd` extension in `php.ini`.
   - Restart Apache / PHP service after editing `php.ini`.

3. Configure database
   - Create a MySQL database named `mr_robot` or update `configs/db.php` with your database settings.
   - Example in `configs/db.php`:
     ```php
     $params_database_main = array(
         'dbhost' => 'localhost',
         'dbuser' => 'mr_robot',
         'dbpass' => '',
         'dbname' => 'mr_robot'
     );
     ```

4. Run migrations
   - Apply `migrations/2026_initial_schema.sql` first to create the schema.
   - Apply `migrations/2026_demo_data.sql` next to insert demo data.
   - Use your preferred MySQL client or command line:
     ```bash
     mysql -u <user> -p mr_robot < migrations/2026_initial_schema.sql
     mysql -u <user> -p mr_robot < migrations/2026_demo_data.sql
     ```

5. Set file permissions (Windows/Apache)
   - Ensure the web server user can read project files.
   - If using a Linux environment, set proper permissions for the project folder.

6. Open the app
   - Access the project in your browser via your local web server, e.g. `http://localhost/mr_robot`.

## Demo admin credentials

- Login: `admin`
- Password: `superadmin123`

## Notes

- The app currently uses a simple custom MVC-style structure.
- Only `superadmin` role is required for administration features.
- If you change database credentials, update `configs/db.php` accordingly.

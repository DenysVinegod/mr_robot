# mr. R O B O T
Project for accounting of business activity with a web interface.

After git clone, run 'composer install' in project directory.
In php.ini uncomment 'extencion gd'.

Import database_dump.sql from /vendor/vinegod/mr_robot_dump_example.sql to your database.

Copy /configs/db.php.example to /configs/db.php and fill in your database credentials.

Configure your web server to serve /mr_robot/ as /.

Visit http://localhost/ in your browser. Default login and password are 'admin' and 'admin'.

$_SESSION['message']['info'] and $_SESSION['message']['error'] are used for displaying messages.
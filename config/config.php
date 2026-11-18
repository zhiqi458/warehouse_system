<?php
declare(strict_types=1);

// Connect to the MySQL Server configured in MySQL Workbench (Workbench itself is the client).
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3307);
define('DB_NAME', 'warehouse_management');
define('DB_USER', 'root');
define('DB_PASS', '12345'); // Set this to the password for the MySQL Server account above.
// The current PHP server uses this project folder as its document root (http://localhost:8080/).
define('BASE_PATH', '');
define('APP_NAME', 'Warehouse Management System');

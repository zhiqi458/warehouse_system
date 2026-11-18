# Warehouse Management System

A database-driven warehouse inventory application built with PHP 8+, MySQL/MariaDB, PDO, HTML, CSS, and vanilla JavaScript. It includes an administrator workspace and a read-only user workspace.

## Features

- Secure username and password login, PHP sessions, session ID regeneration, and role-based page access.
- Live dashboard totals and low-stock alerts.
- Inventory search, category filtering, item create/edit/delete, and item movement history.
- Transactional stock IN/OUT recording with row locking and insufficient-stock protection.
- Filterable stock history and printable movement reports.
- Admin user management, role changes, and password resets.
- Responsive layout for desktop, tablet, and mobile.

## Roles

Admins can access Dashboard, Items, Stock Movements, Reports, and Users. Users can access the Dashboard and read-only Items pages. Admin routes check the session role on every request.

## Requirements

- PHP 8.0 or later with PDO MySQL enabled
- MySQL 8+ or MariaDB with InnoDB
- Apache web server (XAMPP Apache can serve the PHP app; XAMPP's MySQL service is optional)

No Node.js or frontend build step is needed.

## Database setup with MySQL Workbench

1. Start the MySQL Server configured in MySQL Workbench. Workbench is the database client; PHP connects to the server using its host, port, username, and password.
2. In Workbench, open and execute `database/schema.sql`. It creates/selects `warehouse_management` and creates its tables, demo users, five items, and sample movement history.
3. Edit `config/config.php` to match the Workbench connection's server details: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS`.
4. Set `BASE_PATH` to the installation URL path. For a folder at `htdocs/Warehouse`, use `/Warehouse`; for a folder directly at `htdocs/warehouse_system`, use `/warehouse_system`. Use an empty string if the project is the web root.
5. Open the matching URL in your browser and sign in.

### Demo accounts

| Role | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `admin123` |
| User | `demo` | `user123` |

Change demo passwords after installation. Passwords created and updated in the application are stored with PHP `password_hash()`.

## XAMPP installation

Copy this project into an Apache web root such as `C:\xampp\htdocs\Warehouse`, create/import the schema using MySQL Workbench, configure the MySQL Server connection values, and set `BASE_PATH` to `/Warehouse`. Start Apache and the MySQL Server, then visit `http://localhost/Warehouse`.

## cPanel deployment

Upload the project to `public_html/warehouse` or `public_html`, create a MySQL database and user in cPanel, grant the user privileges, and import `database/schema.sql` into that database. cPanel may prefix the database name; in that case, remove or edit the `CREATE DATABASE` and `USE warehouse_management` lines at the top of the SQL file, and set `DB_NAME` to the exact assigned name. Update the database credentials and `BASE_PATH` in `config/config.php`. Enable HTTPS in the hosting control panel. The secure session cookie flag is enabled automatically when PHP detects HTTPS.

## Security notes

- Database access uses PDO prepared statements and exceptions.
- All dynamic HTML output is escaped with `htmlspecialchars()`.
- Forms that modify data use session CSRF tokens and server-side validation.
- Stock transactions use `beginTransaction()`, `SELECT ... FOR UPDATE`, `commit()`, and `rollBack()`.
- The `config` and `database` directories deny direct HTTP access; directory listing is disabled.
- Item and user foreign keys use `ON DELETE RESTRICT` to preserve movement history.

## Project structure

```text
admin/       Admin dashboard, inventory, movements, reports, and users
api/         Authenticated item lookup endpoint
assets/      CSS and vanilla JavaScript
config/      Database configuration and PDO connection
database/    Importable schema and sample records
includes/    Authentication, shared helpers, layout, and navigation
user/        Read-only dashboard, inventory, and item detail pages
```

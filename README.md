# CCALM PHP + MySQL

## Requirements
- PHP 8.1+
- MySQL 5.7+ / 8.x or MariaDB equivalent
- PDO MySQL extension
- Apache/Nginx/local PHP server

## Install
1. Create a folder named `ccalm` inside your local web root.
2. Copy this package into it.
3. Import `schema.sql` into MySQL/phpMyAdmin.
4. Edit `config.php`:
   - database host
   - database name
   - database user
   - database password
   - admin password
5. Visit `/ccalm/index.php`.
6. Admin dashboard: `/ccalm/admin/login.php`.

## Example XAMPP
`C:\xampp\htdocs\ccalm\`

Then:
`http://localhost/ccalm/`

## Example MAMP
Put the folder inside the configured document root and visit the corresponding local URL.

## Production checklist
- Change `admin_password`
- Change DB credentials
- Move config.php outside public root if possible
- Enable HTTPS
- Add CSRF protection (already included in admin forms)
- Add email/SMS confirmations
- Add GDPR/privacy notice and consent where appropriate
- Replace demo menu items with the restaurant's verified current menu
- Confirm opening hours and seating capacity with the client

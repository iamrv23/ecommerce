# Security & Setup Guide

This guide helps you securely set up and run the E-Commerce Platform project.

## Before Running the Project

### 1. Environment Variables Setup

1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your actual configuration:
   ```bash
   nano .env
   ```

3. Update the following values for your environment:
   - **Database credentials**: Change `DB_USER`, `DB_PASSWORD`, `DB_NAME`
   - **Security key**: Generate a new `COOKIE_VALIDATION_KEY` using:
     ```bash
     php -r 'echo bin2hex(random_bytes(32));'
     ```
   - **Admin code**: Change `ADMIN_SIGNUP_CODE` to something unique
   - **Email addresses**: Update `ADMIN_EMAIL` and `SENDER_EMAIL`

### 2. Environment Loading

The application loads environment variables automatically. If you prefer manual configuration:

```php
// In config files, environment variables are accessed via:
getenv('DB_HOST')
getenv('ADMIN_EMAIL')
// etc.
```

## Important Security Practices

### ✅ DO:
- Change all default credentials in `.env`
- Keep `.env` file in `.gitignore` (already configured)
- Use strong, unique passwords for database access
- Regenerate `COOKIE_VALIDATION_KEY` in production
- Store sensitive data in environment variables only
- Use HTTPS in production environments
- Enable database password authentication
- Review and test RBAC permissions regularly

### ❌ DON'T:
- Commit `.env` files to version control
- Use default admin signup codes
- Share credentials in documentation or code
- Leave `enableCsrfValidation` disabled in production
- Hardcode secrets in any PHP files
- Use 'root' user with empty password in production
- Leave `useFileTransport` enabled for mail in production

## Database Setup

### Creating Database Locally

```bash
# Login to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE ecommerce_db;
CREATE USER 'ecommerce_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON ecommerce_db.* TO 'ecommerce_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Running Migrations

```bash
php yii migrate
```

## Running the Application

### Development Environment

```bash
# Set PHP environment
export YII_ENV=dev

# Start built-in server (or use MAMP/XAMPP)
php yii serve
```

### Production Environment

```bash
# Set PHP environment
export YII_ENV=prod

# Key configurations for production:
# 1. Disable debug mode
# 2. Enable schema caching in config/db.php
# 3. Set proper file permissions
# 4. Use a proper web server (Nginx/Apache)
# 5. Enable HTTPS
```

## File Permissions

Ensure proper permissions for runtime directories:

```bash
chmod -R 777 runtime/
chmod -R 777 web/uploads/
```

## Session & Cache Security

- Session data is stored in files by default
- For production, consider using Redis or database sessions
- Clear cache before deploying: `php yii cache/flush-all`

## Default User Accounts

For development purposes, check the `models/User.php` file for demo accounts. **Always disable or change these in production.**

## Testing

### Running Tests

```bash
# Run all tests
php bin/codecept run

# Run specific test suite
php bin/codecept run unit
php bin/codecept run functional
php bin/codecept run acceptance
```

## Reporting Security Issues

If you discover a security vulnerability, please email security@example.com instead of using the issue tracker.

**Do not publicly disclose vulnerabilities until a fix is available.**

## Additional Resources

- [Yii2 Security Best Practices](https://www.yiiframework.com/doc/guide/2.0/en/security-best-practices)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security](https://www.php.net/manual/en/security.php)

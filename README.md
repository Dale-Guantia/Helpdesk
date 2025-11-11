
# LARAVEL HELPDESK WITH FILAMENT

This project aims to provide a web-based helpdesk system using Laravel 12 and Filament v3. A helpdesk is a system that allows users to submit questions, request assistance, or report issues related to an organization’s services.



## System Requirements

- PHP 8.2 or higher
- Composer
- Database (eg: MySQL, PostgreSQL, SQLite)
- Web Server (eg: Apache, Nginx, IIS)


## Installation

Clone the repository: 
```bash
git clone https://github.com/Dale-Guantia/Helpdesk.git
```

Configure ".env" file (eg: DB_CONNECTION, QUEUE_CONNECTION=sync)

Run the command below to create .env copy:
```bash
cp .env.example .env
```

Install PHP dependencies:
```bash
composer install
```

Install front-end dependencies:
```bash
npm install
```

Generate application key: 
```bash
php artisan key:generate
```

Create a symlink to the storage:
```bash
php artisan storage:link
```

Setting Up Notifications:
```bash
php artisan queue:work
```

Run database migration:
```bash
php artisan migrate --seed
```

Run the server:
```bash
php artisan serve
```

## Dummy Accounts

#### Note: 
These dummy accounts work right away without needing email verification because the "email_verified_at" field in the database is hardcoded via seeders. However, if you create a new account through the registration page, you'll need to set up an SMTP testing tool like Mailpit to test the email verification feature.

#### Super Admin
Email: superadmin@example.com.<br>
Password: 12341234

#### Division Head
Email: divisionhead@example.com.<br>
Password: 12341234

#### Staff
Email: staff@example.com.<br>
Password: 12341234

#### Regular User / Employee
Email: employee@example.com.<br>
Password: 12341234

## Mailpit Setup

1. #### Visit the installation page:
https://mailpit.axllent.org/docs/install/

2. #### Download Mailpit:
Scroll down to the section titled "Download static binary (Windows, Linux, and Mac)" and click the Releases link.
Or directly go to the GitHub releases page:
https://github.com/axllent/mailpit/releases/latest

Download the appropriate file for your operating system (Windows, Linux, or Mac).

3. Extract the downloaded file. The folder should contain three files. Then open the **extracted folder** in your terminal or command prompt. 

4. #### Start Mailpit:
In your terminal, type:
```
mailpit
``` 

You should see output like this:
```
time="2025/06/30 16:28:10" level=info msg="[smtpd] starting on [::]:1025 (no encryption)"
time="2025/06/30 16:28:10" level=info msg="[http] starting on [::]:8025"
time="2025/06/30 16:28:10" level=info msg="[http] accessible via http://localhost:8025/"
```

5. #### Open Mailpit in your browser:
Visit: http://localhost:8025/

6. #### Configure Laravel Mail Settings:
Open your .env file and update the following lines:
```
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### You're all set!
Email verification should now work, and all test emails will be visible at http://localhost:8025/

## License
The project is open-sourced software licensed under the [MIT license](https://choosealicense.com/licenses/mit/).


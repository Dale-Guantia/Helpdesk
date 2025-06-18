
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

Note: Before running composer install, make sure the folder name Widgets under App\Filament\Widgets starts with a capital "W" (i.e., **Widgets**, not widgets).
```bash
composer install
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

#### Super Admin
>Email: superadmin@example.com<br>
>Password: 12341234

#### Division Head
>Email: divisionhead@example.com<br>
>Password: 12341234

#### Staff
>Email: staff@example.com<br>
>Password: 12341234

#### Regular User / Employee
>Email: employee@example.com<br>
>Password: 12341234

## License
The project is open-sourced software licensed under the [MIT license](https://choosealicense.com/licenses/mit/).


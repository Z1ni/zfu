# Zini's File Upload
Copyright &copy; 2017 Zini

See LICENSE.md

## Requirements
- PHP 7
- Composer
- Database (MySQL, PostgreSQL or SQLite)
- FFMpeg

## Optional
For image optimization:
- Optipng
- Jpegoptim

## Installation
0. Clone this repo to a safe place (**NOT WWW-ROOT**) and symlink from the www folder to `public/`
   ```bash
   $ cd /var/www/
   $ ln -s /path/to/source/public upload
   ```
1. Run Composer
   ```bash
   $ cd /repo/location/
   $ composer install
   ```
2. Prepare your database: if you're using SQLite, you just need to create the database file (`$ touch upload.db`), otherwise you'll need to create a database in your DBMS.
3. Copy `.env.example` to `.env` and set:
   1. `APP_URL` - This should be the full URL to the installation location without a trailing slash, e.g. "http://example.com/up"
   2. `DB_CONNECTION` - Database type, can be "mysql", "postgres" or "sqlite"
   3. `DB_HOST` - Database host (doesn't need to be set if using SQLite)
   4. `DB_PORT` - Database port (doesn't need to be set if using SQLite)
   5. `DB_DATABASE` - Database name. This should be an absolute file path if using SQLite.
   6. `DB_USERNAME` - Database username (doesn't need to be set if using SQLite)
   7. `DB_PASSWORD` - Database password (doesn't need to be set if using SQLite)
4. Set config options in `config/upload.php`. You should check at least the external program paths (FFMpeg, Optipng, Jpegoptim)
5. Run the initialization command and answer some questions
   ```bash
   $ php artisan upload:initialize
   ```
6. Things should now work!

## About file optimization
File optimization requires a running Laravel queue worker. The simpliest way to run the worker is to run `$ php artisan queue:work &` and let it worry about the jobs. Another and maybe more controllable way is to use Supervisor (see [here](https://laravel.com/docs/5.4/queues#supervisor-configuration)).

## REST API
### TODO
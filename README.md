# Livewire Calendar Component

## Requirement
- By default database using Postgres version 17.4, can be found here for [download](https://www.postgresql.org/download/)
- PHP v8.2.25 since this repo is built, you can use any version of ^8.2 since the core always using `readonly class`
- Node JS v23.6.0 or LTS

## Setup
1. Environment
    Copy .env.example to .env as the laravel application usually
    Please note that replace the DB_* according your Database setup in this case the repo would contains as below
   
   ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=adventurous_glue
    DB_USERNAME=postgres
    DB_PASSWORD=password
   ```

   the run this to update APP_KEY
   
   ```
   php artisan key:generate
   ```
3. Database
    Create your database under `adventurous_glue` in your postgres database
4. Update Data
    Init the current Data payload
   ```php
    php artisan migrate
    php artisan db:seed
   ```
5. Pre-test
   to check if it working normal before test in locally, run this
   ```php
    php artisan test
   ```
6. Build client server
    To update hot reloading in client side
   ```
   npm install
   npm run dev
   ```
7. Open in browser
   to expose the laravel application in outside world, run this command, note that you might need to open new tab of terminal

   ```
   php artisan serve
   ```

   Enjoy to preview a demo of livewire component, open link http://127.0.0.1:8000 or if you are using Herd http://livewire-calendar.test
   
## Preview
![image](https://github.com/user-attachments/assets/cf1f1cca-d0c2-4721-bc75-fae3f3fc233f)

## Demo
https://imgur.com/z8dD3rS

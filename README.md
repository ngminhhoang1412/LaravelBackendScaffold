# **Laravel Boilerplate**
<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


## Overview

This is a scaffold (boilerplate) for a standard Laravel project. Fork this project to continue using and receive updates from upstream

Feature (v1.0.0)
+ Sanctum authentication
+ Basic CRUD by default for every model defined
+ Basic query functions based on [FilterQueryString library](https://github.com/mehradsadeghi/laravel-filter-querystring) including
  + Filter
  + Sort
  + Comparisons
  + In
  + Like
  + Where clause
+ More query functions
  + Relation
  + Pagination
  + Limit
  + Order by
  + Relation count

Next version
+ Boilerplate for Frontend


## Requirement & Installation

Create file .env with format like this

```sh
# Database information.
DB_DATABASE=
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_USERNAME=
DB_PASSWORD=
# Encryption keys
IV=<IV>
ENCRYPTION_KEY=<EncryptionKey>
ADMIN_EMAIL=
ADMIN_USERNAME=
ADMIN_PASSWORD=
# Common info
APP_KEY=
APP=production
```


Setup database, then run
```console
composer install
php artisan migrate
php artisan db:seed
sudo chmod -R 755 <folder>
chmod -R o+w <folder>/storage
php artisan key:generate
php artisan cache:clear 
php artisan config:clear
```

## Usage

TODO


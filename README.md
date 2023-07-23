# Permissions
 ![Release Version](https://img.shields.io/github/v/release/live-controls/permissions)
 ![Packagist Version](https://img.shields.io/packagist/v/live-controls/permissions?color=%23007500)
 
 Add permissions to your users or groups.

## Requirements
- Laravel 8.0+
- PHP 8.0+


## Translations
None


## Installation
```
composer require live-controls/permissions
```

## Setup
#### Set root users (default is user with id 1)
1) Run in console:
```
php artisan vendor:publish --tag="livecontrols.permissions.config"
```
2) Open /config/livecontrols_permissions.php
3) Change line "root_users" to an array of user ids


## Usage
Todo

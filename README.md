A Laravel package for the Repository design pattern
=======================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nalingia/laravel-repositories.svg?style=flat-square)](https://packagist.org/packages/nalingia/laravel-repositories)
[![Build Status](https://travis-ci.org/nalingia/laravel-repositories.svg?branch=master)](https://travis-ci.org/nalingia/laravel-repositories)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/nalingia/laravel-repositories.svg?style=flat-square)](https://packagist.org/packages/nalingia/laravel-repositories)

Repositories is a simple package to simplify the creation of scaffolding code when using the [Repository Design Pattern](https://msdn.microsoft.com/en-us/library/ff649690.aspx).
It adds an artisan command which creates the repository and the related contract.
## Installation
You can install the package via composer:
```bash
composer require nalingia/repositories
```

Then you have to add the related Service Provider to the `providers` configuration array in `config/app.php`:
```php
'providers' => [
  ...
  Nalingia\Repositories\RepositoriesServiceProvider::class,
  ...
]
```
## Usage
The package provides an additional artisan command called `make:repository` which accepts as only parameter the model's class name.

For instance, if you need to create a repository of users you can type:

```bash
php artisan make:repository User
```

The command will create both the repository and the related contract in `app/Repositories` and `app/Repositories/Contracts` folders, respectively.
Default folders and namespaces can be changed 

### Available API
The generated repositories will inherit from `Nalingia\Repositories\AbstractEloquentRepository` which provides a bunch of useful and general purpose methods:
* Get all models:
```php
public function all(array $with = []); 
```
* Get model having the given id:
```php
public function findById($id, array $with = [], $fail = true);
```
* Get the first model having `$key` attribute equals to `$value`:
```php
public function getFirstBy($key, $value, array $with = [], $comparator = '=', $fail = false);
```
* Get all models having `$key` attribute equals to `$value`:
```php
public function getManyBy($key, $value, array $with = [], $comparator = '=');
```
* Get first model matching `$where` clauses:
```php
public function getFirstWhere(array $where, $with = [], $fail = false);
```
* Get all models matching `$where` clauses:
```php
public function getAllWhere($where, $with = [], $columns = ['*']);
```
* Get paginated models:
```php
public function getByPage($page = 1, $limit = 10, array $with = []);
```
* Create a new model:
```php
public function create(array $data);
```
* Update a model:
```php
public function update($model, array $data);
```
* Delete a model:
```php
public function delete($model);
```
* Truncate the table related to the model:
```php
public function truncate();
```
* Get all models having ```column_name``` in ```$needles```:
```php
public function getAllWhereColumnNameIn(array $needles, array $with = []);
```

You can contribute to the common methods by proposing a pull request.
### Settings
If you need to change the default command settings, you can publish the `repositories.php` configuration file using the command:

```php
php artisan vendor:publish --provider="Nalingia\Repositories\RepositoriesServiceProvider"
```


## Change log
Please, see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing
Coming soon!

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
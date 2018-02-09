# php-filesystem

This librairy is a set of functions to ease filesystem operations and manipulations

[![Build Status](https://travis-ci.org/hugsbrugs/php-filesystem.svg?branch=master)](https://travis-ci.org/hugsbrugs/php-filesystem)

[![Coverage Status](https://coveralls.io/repos/github/hugsbrugs/php-filesystem/badge.svg?branch=master)](https://coveralls.io/github/hugsbrugs/php-filesystem?branch=master)

## Install

Install package with composer
```
composer require hugsbrugs/php-filesystem
```

In your PHP code, load librairy
```
require_once __DIR__ . '/../vendor/autoload.php';
use Hug\FileSystem\FileSystem as FileSystem;
```

## Usage

List files in a directory optionnaly filter by extension (. and .. are removed from response)
```php
FileSystem::scandir_h($directory, $file_extension = null);
```

Remove files recursively in a directory
```php
FileSystem::rrmdir($dir, $del_dir = TRUE);
```

Recursively copy files and folder to destination and creates directory structure if necessary
```php
FileSystem::rcopy($source, $dest, $permissions = 0755);
```

Writes data in a file and creates directories if necessary
```php
FileSystem::force_file_put_contents($filename, $data, $flags = 0, $context = null);
```

List files in a directory with options
```php
FileSystem::list_dir($dir, $return_type = 'ALL', $return_format = 'FULL');
```

Get file last modification date in desired date format
```php
FileSystem::file_last_mod($file_path, $date_format = 'Y-m-d H:i:s');
```

Get file size
```php
FileSystem::file_size($FilePath);
```

Get bunch of informations about file list
```php
FileSystem::get_file_list_infos($files);
```

Get a human readable file size
```php
FileSystem::human_file_size($size, $unit = '');
```

Converts a file size in bytes in a human readable way
```php
FileSystem::get_symbol_by_quantity($bytes);
```

Get a directory size
```php
FileSystem::dir_size($directory);
```

Get disk usage
```php
FileSystem::get_disk_usage($path);
```

Delete files in a directory older than a given date
```php
FileSystem::remove_older_files($directory, $date_interval = 'P8D', $get_results = false, $test_mode = false);
```

Compares two files for eqality
```php
FileSystem::are_files_equal($file_a, $file_b);
```

Get unix file permissions
```php
FileSystem::unix_file_permissions($path);
```

## Author

Hugo Maugey [visit my website ;)](https://hugo.maugey.fr)

[![Coverage Status](https://coveralls.io/repos/github/hugsbrugs/php-filesystem/badge.svg?branch=master)](https://coveralls.io/github/hugsbrugs/php-filesystem?branch=master)

## php-filesystem

```
composer require hugsbrugs/php-filesystem
```

Load Proxies for first Time
```
require_once __DIR__ . '/../vendor/autoload.php';
use Hug\FileSystem\FileSystem as FileSystem;

FileSystem::scandir_h($directory, $file_extension = null);
```


Remove files recursively in a directory
```
FileSystem::rrmdir($dir, $del_dir = TRUE);
```

Writes data in a file and creates directories if necessary
```
FileSystem::force_file_put_contents($filename, $data, $flags = 0, $context = null);
```

List files in a directory with options
```
FileSystem::list_dir($dir, $return_type = 'ALL', $return_format = 'FULL');
```

Get file last modification date in desired date format
```
FileSystem::file_last_mod($file_path, $date_format = 'Y-m-d H:i:s');
```

Get file size
```
FileSystem::file_size($FilePath);
```

get bunch of informations about file list
```
FileSystem::get_file_list_infos($files);
```

Get a human readable file size
```
FileSystem::human_file_size($size, $unit = '');
```

Converts a file size in bytes in a human readable way
```
FileSystem::get_symbol_by_quantity($bytes);
```

Get a directory size
```
FileSystem::dir_size($directory);
```

Get disk usage
```
FileSystem::get_disk_usage($path);
```

Delete files in a directory older than a given date
```
FileSystem::remove_older_files($directory, $date_interval = 'P8D', $get_results = false, $test_mode = false);
```

Compares two files for eqality
```
FileSystem::are_files_equal($file_a, $file_b);
```

Get unix file permissions
```
FileSystem::unix_file_permissions($path);
```

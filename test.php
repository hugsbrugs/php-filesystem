<?php

require_once __DIR__ .'/vendor/autoload.php';

use Hug\FileSystem\FileSystem as FileSystem;

$directory = '/home/hugo/Téléchargements';

$file_extension = null;
// $scan = FileSystem::scandir_h($directory, $file_extension);
// error_log(print_r($scan, true));

$scan = FileSystem::unix_file_permissions($directory);
error_log(print_r($scan, true));
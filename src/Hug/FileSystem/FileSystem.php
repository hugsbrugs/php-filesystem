<?php

namespace Hug\FileSystem;

/**
 *
 */
class FileSystem
{
    /**
     * List all files in directory (and sub directories) optionnaly filtered by extension
     *
     * @param string $directory Directory to list files
     * @param string $file_extension File Extension to filter by
     *
     * @return array $scanned_directory Array of filenames in the directory
     *
     */
    public static function scandir_h($directory, $file_extension = null)
    {
        $scanned_directory = [];

        try
        {
            # Test directory exists and is readable
            if(is_dir($directory) && is_readable($directory))
            {
                $di = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
                $it = new \RecursiveIteratorIterator($di);

                foreach($it as $file)
                {
                    if($file_extension===null)
                    {
                        $scanned_directory[] = $file->getFilename();
                    }
                    else
                    {
                        if(pathinfo($file, PATHINFO_EXTENSION) == $file_extension)
                        {
                            $scanned_directory[] = $file->getFilename();
                        }
                    }
                }
                unset($it);
                unset($di);
            }
            else
            {
                $scanned_directory = false;
            }
        }
        catch(Exception $e)
        {
            $scanned_directory = false;
        }

        return $scanned_directory;
    }

    /**
     * Recursively deletes files and directories from file system
     *
     * @param string $dir Directory to delete
     *
     * @return bool 
     *
     * @todo check what rmdir command returns / modify function to return Result array with debugging mesages
     *
     */
    public static function rrmdir($dir, $del_dir = true)
    {
        $result = false;
        try
        {
            if(is_dir($dir))
            {
                foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path)
                {
                    $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
                }
                if($del_dir === true)
                {
                    rmdir($dir);
                }
                $result = true;
            }
            
        }
        catch(Exception $e)
        {
            error_log('rrmdir : '.$e->getMessage());
        }
        return $result;
    } 

    /**
     * http://stackoverflow.com/questions/13372179/creating-a-folder-when-i-run-file-put-contents
     * create file with content, and create folder structure if doesn't exist 
     *
     * @param String $filename
     * @param String $data
     * @return 
     */
    public static function force_file_put_contents($filename, $data, $flags = 0, $context = null)
    {
        $Response = ['status' => 'error', 'message' => ''];
        try 
        {
            $isInFolder = preg_match("/^(.*)\/([^\/]+)$/", $filename, $filenameMatches);
            if($isInFolder)
            {
                $folderName = $filenameMatches[1];
                $fileName = $filenameMatches[2];
                if (!is_dir($folderName))
                {
                    if(is_writable(dirname($folderName)))
                    {
                        mkdir($folderName, 0777, true);
                    }
                    else
                    {
                        $Response['message'] = 'WRITE_PERMISSION_DENIED';
                        return $Response;
                    }
                }
            }
            //return file_put_contents($filename, $data, $flags, $context);
            $result = file_put_contents($filename, $data, $flags, $context);
            if($result===FALSE)
            {
                $Response['message'] = 'UNKNOWN_WRITE_ERROR';
            }
            else
            {
                $Response['status'] = 'success';
            }
            return $Response;
        }
        catch (Exception $e)
        {
            $Response['message'] = 'EXCEPTION';
            error_log('force_file_put_contents : ' . $e->getMessage());
        }
        return $Response;
    }


    /**
     * List files and dir in a dir
     *
     * @param string $dir directory to parse
     * @param string $return_type return type can be ALL, FILE or DIR
     * @param string $return_format return format can be FULL (full path) or SHORT (only file name)
     *
     * @return array list of filtered files and dir 
     */
    public static function list_dir($dir, $return_type = 'ALL', $return_format = 'FULL')
    {
        $list = FALSE;

        $return_types = ['ALL', 'FILE', 'DIR'];
        $return_formats = ['FULL', 'SHORT'];

        # CHECK OPTIONS ARE CORRECT
        if(in_array($return_type, $return_types) && in_array($return_format, $return_formats))
        {
            # FORCE TRAILING SLASH ON DIR PATH
            $dir = rtrim($dir, '/') . DIRECTORY_SEPARATOR;

            
            if(is_dir($dir))
            {
                $files = scandir($dir);

                if(count($files) > 0)
                {
                    $list = [];
                    foreach ($files as $file)
                    {
                        if($file!='.' && $file!='..')
                        {
                            $add_file = FALSE;
                            switch ($return_type)
                            {
                                case 'ALL':
                                    $add_file = TRUE;
                                    
                                    break;
                                case 'FILE':
                                    if(is_file($dir.$file))
                                    {
                                        $add_file = TRUE;
                                    }
                                    break;
                                case 'DIR':
                                    if(is_dir($dir.$file))
                                    {
                                        $add_file = TRUE;
                                    }
                                    break;
                                default:
                                    error_log("BAD return_type in list_dir");
                                    break;
                            }
                            if($add_file === TRUE)
                            {
                                switch ($return_format)
                                {
                                    case 'FULL':
                                        $list[] = $dir.$file;
                                        break;
                                    case 'SHORT':
                                        $list[] = $file;
                                        break;
                                    default:
                                        error_log("BAD return_format in list_dir");
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
        else
        {
            error_log("BAD OPTIONS in return_types OR return_formats");
        }
        
        return $list;
    }

    /**
     * Get file last modification's date
     *
     * @param string $file_path
     * @param string $date_format
     *
     * @return date|FALSE $file_last_mod File last modification date in format 'Y-m-d H:i:s' or FALSE if file does not exists.
     *
     */
    public static function file_last_mod($file_path, $date_format = 'Y-m-d H:i:s')
    {
        $file_last_mod = FALSE;
        if (file_exists($file_path))
        {
            $file_last_mod = date($date_format, filemtime($file_path));
        }
        return $file_last_mod;
    }

    /**
     * Get file size
     *
     * @param string $FilePath
     *
     * @return int|null $FileSize File size in bytes or null if file does not exists.
     *
     */
    public static function file_size($FilePath)
    {
        $FileSize = null;
        if (file_exists($FilePath))
        {
            $FileSize = filesize($FilePath);
        }
        return $FileSize;
    }

    /**
     * @param array $files array of file paths to get info from
     * @return array 
     */
    public static function get_file_list_infos($files)
    {
        $list_info = [];
        try
        {
            foreach ($files as $file)
            {
                // $file_type = '';
                // if(is_file($file))
                //     $file_type = 'file';
                // if(is_dir($file))
                //     $file_type = 'dir';

                // (  )

                // $file_last_mod = file_last_mod($file);
                // $file_size = file_size($file);
                // $file_name = basename($file);
                // $file_path = pathinfo($file, PATHINFO_DIRNAME);

                $list_info[] = [
                    'type' => is_file($file) ? 'file' : (is_dir($file) ? 'dir' : '' ), 
                    'name' => basename($file), 
                    'path' => pathinfo($file, PATHINFO_DIRNAME), 
                    'last_mod' => Filesystem::file_last_mod($file), 
                    'size' => Filesystem::file_size($file)
                ];
            }
        }
        catch(Exception $e)
        {
            error_log('get_file_list_infos : ' . $e->getMessage());
        }
        return $list_info;
    }

    /**
     * Converts a file size in bytes in a human readable way
     *
     * @param int $size file size in bytes
     * @param string $unit (TB, GB, MB, KB)
     * @return string $file_size human readable file size
     *
     */
    public static function human_file_size($size, $unit = '')
    {
        if(is_numeric($size))
        {
            if( (!$unit && $size >= 1<<40) || $unit == "TB")
            {
                return number_format($size/(1<<40),2) . " TB";
            }
            if( (!$unit && $size >= 1<<30) || $unit == "GB")
            {
            return number_format($size/(1<<30),2) . " GB";
            }
            if( (!$unit && $size >= 1<<20) || $unit == "MB")
            {
                return number_format($size/(1<<20),2) . " MB";
            }    
            if( (!$unit && $size >= 1<<10) || $unit == "KB")
            {
                return number_format($size/(1<<10),2) . " KB";
            }
            return number_format($size)." bytes";
        }
        else
        {
            return false;
        }
    }

    /**
     * Converts a file size in bytes in a human readable way
     *
     * @param int $bytes
     *
     * @return string $formated_bytes human readable file size
     *
     */
    public static function get_symbol_by_quantity($bytes)
    {
        if(is_numeric($bytes))
        {
            $symbols = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
            $exp = floor( log($bytes) / log(1024) );
            return sprintf('%.2f ' . $symbols[$exp], ( $bytes / pow(1024, floor($exp)) ) );
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns a directory size
     *
     * @param string $directory
     *
     * @return int $size directory size (in bytes ?)
     *
     */
    public static function dir_size($directory)
    {
        $size = false;

        if(is_dir($directory))
        {
            $size = 0;
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file)
            {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }

    /**
     * Get statistics about system file folder
     *
     * @param string $path
     *
     * @return array disk_usage (disk_free_space, disk_space_used, disk_space_total, disk_used_percentage)
     *
     */
    public static function get_disk_usage($path)
    {
        $disk_usage = false;
        
        if(is_readable($path))
        {
            $disk_usage = [];

            # get disk space free (in bytes)
            $df = disk_free_space($path);
            # and get disk space total (in bytes)
            $dt = disk_total_space($path);
            # now we calculate the disk space used (in bytes)
            $du = $dt - $df;
            # percentage of disk used - this will be used to also set the width % of the progress bar
            $dp = sprintf('%.2f',($du / $dt) * 100);

            $disk_usage['disk_free_space'] = FileSystem::human_file_size($df);
            $disk_usage['disk_space_used'] = FileSystem::human_file_size($du);
            $disk_usage['disk_space_total'] = FileSystem::human_file_size($dt);
            $disk_usage['disk_used_percentage'] = $dp;
        }

        return $disk_usage;
    }

    /**
     * Deletes files older than a given date interval
     *
     * Usefull for cleaning test repositories, log files, temporary files, ...
     *
     * @param string $directory
     * @param string $date_interval in format P8D
     * @param bool $get_results get list of deleted files
     * @param bool $test_mode just performs test (used with get_results false)
     *
     * @return array $result
     *
     */
    public static function remove_older_files($directory, $date_interval = 'P8D', $get_results = false, $test_mode = false)
    {
        $result = ['status' => 'error', 'message' => '', 'data' => []];

        $errors = 0;
        if(is_dir($directory))
        {
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)) as $file)
            {
                $file_last_mod = new DateTime('@'.filemtime($file));

                if($file_last_mod!==null)
                {
                    # compare file_last_mod with now less date interval
                    if($file_last_mod < (new DateTime('now'))->sub(new DateInterval($date_interval)) )
                    {
                        # if in real mode : delete file
                        if($test_mode===false)
                        {
                            $deleted = unlink($file);
                        }
                        else
                        {
                            # test mode : emulate delete
                            $deleted = true;
                        }

                        # if file has been deleted
                        if($deleted)
                        {
                            # if we send back results
                            if($get_results)
                            {
                                # add file name to list of deleted files
                                $result['data'][] = $file->getPathname();
                            }
                        }
                        else
                        {
                            # error deleting file
                            $result['message'] .= 'ERROR DELETING : ' . $file;
                            $errors++;
                        }
                    }
                    // else
                    // {
                    //     # file is too recent to be deleted
                    // }
                }
                else
                {
                    # error deleting file
                    $result['message'] .= 'ERROR GET FILE LAST MOD : ' . $file;
                    $errors++;
                }
            }
        }
        else
        {
            $result['message'] .= 'NOT_A DIRECTORY : ' . $directory;
        }

        if($errors===0)
        {
            $result['status'] = 'success';
        }

        return $result;
    }

    /**
     * Checks if files are identical based on md5
     *
     * @param string $file_a file path to file a
     * @param string $file_b file path to file b
     *
     * @return bool are_files_equal
     */
    public static function are_files_equal($file_a, $file_b)
    {
        $md5image1 = md5(file_get_contents($file_a));
        $md5image2 = md5(file_get_contents($file_b));
        if ($md5image1 == $md5image2)
        {
            return true;
        }
        return false;
    }
    /*public static function are_files_equal($file_a, $file_b)
    {
        if (filesize($file_a) == filesize($file_b))
        {
            $fp_a = fopen($file_a, 'rb');
            $fp_b = fopen($file_b, 'rb');

            while (($b = fread($fp_a, 4096)) !== false)
            {
                $b_b = fread($fp_b, 4096);
                if ($b !== $b_b)
                {
                    fclose($fp_a);
                    fclose($fp_b);
                    return false;
                }
            }

            fclose($fp_a);
            fclose($fp_b);

            return true;
        }

        return false;
    }*/

    /**
     * Returns file permissions in unix format -rw-r--r--
     *
     * @param $path file or dir path
     *
     * @return $permissions
     *
     */
    public static function unix_file_permissions($path)
    {
        $info = false;

        if(is_readable($path))
        {
            $info = '';

            $perms = fileperms($path);

            if(($perms & 0xC000) == 0xC000)
            {
                # Socket
                $info = 's';
            }
            elseif(($perms & 0xA000) == 0xA000)
            {
                # Symbolic Link
                $info = 'l';
            }
            elseif(($perms & 0x8000) == 0x8000)
            {
                # Regular
                $info = '-';
            }
            elseif(($perms & 0x6000) == 0x6000)
            {
                # Block special
                $info = 'b';
            }
            elseif(($perms & 0x4000) == 0x4000)
            {
                # Directory
                $info = 'd';
            }
            elseif(($perms & 0x2000) == 0x2000)
            {
                # Character special
                $info = 'c';
            }
            elseif(($perms & 0x1000) == 0x1000) 
            {
                # FIFO pipe
                $info = 'p';
            }
            else {
                # Unknown
                $info = 'u';
            }

            # Owner
            $info .= (($perms & 0x0100) ? 'r' : '-');
            $info .= (($perms & 0x0080) ? 'w' : '-');
            $info .= (($perms & 0x0040) ?
                        (($perms & 0x0800) ? 's' : 'x' ) :
                        (($perms & 0x0800) ? 'S' : '-'));

            # Group
            $info .= (($perms & 0x0020) ? 'r' : '-');
            $info .= (($perms & 0x0010) ? 'w' : '-');
            $info .= (($perms & 0x0008) ?
                        (($perms & 0x0400) ? 's' : 'x' ) :
                        (($perms & 0x0400) ? 'S' : '-'));

            # World
            $info .= (($perms & 0x0004) ? 'r' : '-');
            $info .= (($perms & 0x0002) ? 'w' : '-');
            $info .= (($perms & 0x0001) ?
                        (($perms & 0x0200) ? 't' : 'x' ) :
                        (($perms & 0x0200) ? 'T' : '-'));
        }

        return $info;
    }

}

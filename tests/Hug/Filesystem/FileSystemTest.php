<?php

# For PHP7
// declare(strict_types=1);

// namespace Hug\Tests\FileSystem;

use PHPUnit\Framework\TestCase;

use org\bovigo\vfs\vfsStream,
    org\bovigo\vfs\vfsStreamDirectory;

use Hug\FileSystem\FileSystem as FileSystem;

// assertDirectoryExists()
// assertDirectoryIsReadable()
// assertDirectoryIsWritable()
// assertEmpty()
// $this->expectException(UnexpectedValueException::class);

/**
 * @covers FileSystem
 */
final class FileSystemTest extends TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    /**
     * set up test environmemt
     */
    public function setUp()
    {
        # Create Virtual File System
        $this->root = vfsStream::setup('exampleDir', 0755);

        # Create Virtual Filesystem Structure
        // $structure = array(
        //     'Core' => array(
        //         'AbstractFactory' => array(
        //             'test.php' => 'some text content',
        //             'other.php' => 'Some more text content',
        //             'Invalid.csv' => 'Something else',
        //         ),
        //     'AnEmptyFolder' => array(),
        //     'badlocation.php' => 'some bad content',
        // ));
        // $this->root = vfsStream::setup('exampleDir', 0755, $structure);

        # Copy Real File Structure
        # https://github.com/mikey179/vfsStream/wiki/CopyFromFileSystem
        // vfsStream::copyFromFileSystem($path, $baseDir, $maxFileSize)
    }

    /* ************************************************* */
    /* ************* FileSystem::scandir_h ************* */
    /* ************************************************* */

    /**
     * @covers Hug\FileSystem\FileSystem::scandir_h
     */
    public function testCanScanDirectoryWithValidAccessRights()
    {
        $this->assertInternalType(
            'array',
            FileSystem::scandir_h(vfsStream::url('exampleDir'))
        );
    }
    
    /**
     * @covers Hug\FileSystem\FileSystem::scandir_h
     */
    public function testCannotScanDirectoryWithInvalidAccessRights()
    {
        // $this->assertFalse( FileSystem::scandir_h('/root') );
        $this->assertFalse( FileSystem::scandir_h(vfsStream::url('root')) );
    }

    /**
     * @covers Hug\FileSystem\FileSystem::scandir_h
     */
    public function testCannotScanDirectoryWithInvalidDirectory()
    {
        // $this->assertFalse( FileSystem::scandir_h('/coucou') );
        $this->assertFalse( FileSystem::scandir_h(vfsStream::url('coucou')) );
    }

    /**
     * @covers Hug\FileSystem\FileSystem::scandir_h
     */
    public function testCanScanDirectoryWithValidFileExtension()
    {
        $this->assertInternalType(
            'array',
            // FileSystem::scandir_h('/home/hugo/Téléchargements', 'txt')
            FileSystem::scandir_h(vfsStream::url('exampleDir'), 'txt')
        );
    }
    
    /**
     * @covers Hug\FileSystem\FileSystem::scandir_h
     */
    public function testCannotScanDirectoryWithInvalidFileExtension()
    {
        // $test = FileSystem::scandir_h('/home/hugo/Téléhargements', 'nothing');
        $test = FileSystem::scandir_h(vfsStream::url('exampleDir'), 'nothing');

        $this->assertInternalType( 'array', $test);
        $this->assertEmpty($test);
    }

    /**
     * @covers Hug\FileSystem\FileSystem::scandir_h
     */
    public function testCanBeUsedAsArray()
    {
        $this->assertEquals(
            ['FileSystemTest.php'],
            FileSystem::scandir_h(__DIR__, 'php')
        );
    }


    /* ************************************************* */
    /* *************** FileSystem::rrmdir ************** */
    /* ************************************************* */

    // rrmdir($dir, $del_dir = true)
    
    /* ************************************************* */
    /* ****** FileSystem::force_file_put_contents ****** */
    /* ************************************************* */

    // force_file_put_contents($filename, $data, $flags = 0, $context = null)
    
    /* ************************************************* */
    /* ************** FileSystem::list_dir ************* */
    /* ************************************************* */

    // list_dir($dir, $return_type = 'ALL', $return_format = 'FULL')
    // $return_types = ['ALL', 'FILE', 'DIR'];
    // $return_formats = ['FULL', 'SHORT'];

    /* ************************************************* */
    /* *********** FileSystem::file_last_mod *********** */
    /* ************************************************* */

    // file_last_mod($file_path, $date_format = 'Y-m-d H:i:s')

    /* ************************************************* */
    /* ************* FileSystem::file_size ************* */
    /* ************************************************* */

    // file_size($FilePath)

    /* ************************************************* */
    /* ******** FileSystem::get_file_list_infos ******** */
    /* ************************************************* */

    // get_file_list_infos($files)

    /* ************************************************* */
    /* ********** FileSystem::human_file_size ********** */
    /* ************************************************* */

    /**
     * @covers Hug\FileSystem\FileSystem::human_file_size
     */
    public function testCanGetHumanFileSizeWithValidAccessRights()
    {
        $test = FileSystem::human_file_size(45545415411);
        $this->assertInternalType('string', $test);
        $this->assertEquals('42.42 GB', $test);

        $test = FileSystem::human_file_size(45415411);
        $this->assertInternalType('string', $test);
        $this->assertEquals('43.31 MB', $test);

        $test = FileSystem::human_file_size(15411);
        $this->assertInternalType('string', $test);
        $this->assertEquals('15.05 KB', $test);

        $test = FileSystem::human_file_size(11);
        $this->assertInternalType('string', $test);
        $this->assertEquals('11 bytes', $test);
    }

    /**
     * @covers Hug\FileSystem\FileSystem::human_file_size
     */
    public function testCannoGetHumanFileSizeWithInvalidAccessRights()
    {
        $test = FileSystem::human_file_size('dzedez');
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* ******* FileSystem::get_symbol_by_quantity ****** */
    /* ************************************************* */

    /**
     * @covers Hug\FileSystem\FileSystem::get_symbol_by_quantity
     */
    public function testCanGetSymbolByQuantityWithValidAccessRights()
    {
        $test = FileSystem::get_symbol_by_quantity(45545415411);
        $this->assertInternalType('string', $test);
        $this->assertEquals('42.42 GiB', $test);

        $test = FileSystem::get_symbol_by_quantity(45415411);
        $this->assertInternalType('string', $test);
        $this->assertEquals('43.31 MiB', $test);

        $test = FileSystem::get_symbol_by_quantity(15411);
        $this->assertInternalType('string', $test);
        $this->assertEquals('15.05 KiB', $test);

        $test = FileSystem::get_symbol_by_quantity(11);
        $this->assertInternalType('string', $test);
        $this->assertEquals('11.00 B', $test);
    }

    /**
     * @covers Hug\FileSystem\FileSystem::get_symbol_by_quantity
     */
    public function testCannoGetSymbolByQuantityWithInvalidAccessRights()
    {
        $test = FileSystem::get_symbol_by_quantity('dzedez');
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* ************** FileSystem::dir_size ************* */
    /* ************************************************* */

    /**
     * @covers Hug\FileSystem\FileSystem::dir_size
     */
    public function testCanGetDirSizeWithValidAccessRights()
    {
        $test = FileSystem::dir_size(vfsStream::url('exampleDir'));
        $this->assertInternalType('integer', $test);
    }

    /**
     * @covers Hug\FileSystem\FileSystem::dir_size
     */
    public function testCannoGetDirSizeWithInvalidAccessRights()
    {
        $test = FileSystem::dir_size(vfsStream::url('caca'));
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* *********** FileSystem::get_disk_usage ********** */
    /* ************************************************* */

    /**
     * @covers Hug\FileSystem\FileSystem::get_disk_usage
     */
    // public function testCanGetDiskUsageWithValidAccessRights()
    // {
        // $test = FileSystem::get_disk_usage(vfsStream::url('exampleDir'));
        // $test = FileSystem::get_disk_usage('/home/hugo/Téléchargements');
        // $this->assertInternalType('array', $test);
        // $this->assertArrayHasKey('disk_free_space', $test);
        // $this->assertArrayHasKey('disk_space_used', $test);
        // $this->assertArrayHasKey('disk_space_total', $test);
        // $this->assertArrayHasKey('disk_used_percentage', $test);
    // }

    /**
     * @covers Hug\FileSystem\FileSystem::get_disk_usage
     */
    public function testCannoGetDiskUsageWithInvalidAccessRights()
    {
        // $test = FileSystem::get_disk_usage(vfsStream::url('caca'));
        $test = FileSystem::get_disk_usage('caca');
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* ********* FileSystem::remove_older_files ******** */
    /* ************************************************* */

    // remove_older_files($directory, $date_interval = 'P8D', $get_results = false, $test_mode = false)

    /* ************************************************* */
    /* ********** FileSystem::are_files_equal ********** */
    /* ************************************************* */

    // are_files_equal($file_a, $file_b)

    /* ************************************************* */
    /* ******* FileSystem::unix_file_permissions ******* */
    /* ************************************************* */

    /**
     * @covers Hug\FileSystem\FileSystem::unix_file_permissions
     */
    public function testCanGetUnixFilePermissionsWithValidAccessRights()
    {
        $test = FileSystem::unix_file_permissions(vfsStream::url('exampleDir'));
        $this->assertInternalType('string', $test);
        // test string length===10
    }

    /**
     * @covers Hug\FileSystem\FileSystem::unix_file_permissions
     */
    public function testCannoGetUnixFilePermissionsWithInvalidAccessRights()
    {
        $test = FileSystem::unix_file_permissions(vfsStream::url('caca'));
        $this->assertFalse($test);
    }

}


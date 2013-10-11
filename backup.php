<?php
define('DIRECTORY_MASK', 0777);

spl_autoload_extensions('.class.php');
spl_autoload_register();

use Skywebro\Backup\Backup;

$exit_code = 0;
$ini = getopt('i:')['i'];

if (empty($ini)) {
    print "Usage: backup -i ini_file\n";
    $exit_code = 1;
} else {
    try {
        Backup::factory($ini)->run();
    } catch (Exception $e) {
        print 'ERROR: ' . $e->getMessage() . "!\n";
        $exit_code = 2;
    }
}

exit($exit_code);

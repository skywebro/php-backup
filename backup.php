<?php
define('BASE_DIR', dirname(__FILE__));
define('DIRECTORY_MASK', 0777);

require_once BASE_DIR . '/backup.class.php';

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

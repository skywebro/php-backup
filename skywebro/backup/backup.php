<?php
define('DIRECTORY_MASK', 0777);

spl_autoload_extensions('.class.php');
spl_autoload_register();

use Skywebro\Backup\Backup;

$exit_code = 0;
$options = getopt('i:');

try {
    $backup = Backup::factory($options['i']);
    $backup->run();
} catch (Exception $e) {
    print "\033[01;31mERROR: " . $e->getMessage() . "!\033[0m\n";
    $exit_code = 1;
}

exit($exit_code);

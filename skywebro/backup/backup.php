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
    $color = (1 === $e->getCode()) ? "0;32" : "01;31";
    print "\033[{$color}m" . $e->getMessage() . "!\033[0m\n";
    $exit_code = $e->getCode();
}

exit($exit_code);

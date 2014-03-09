<?php
define('DIRECTORY_MASK', 0755);

spl_autoload_extensions('.class.php');
spl_autoload_register();

require_once 'log4php/Logger.php';

use Org\Impavidly\Backup\Backup;
use Org\Impavidly\Backup\Exceptions\Exception as BackupException;

$exitCode = 0;
$options = getopt('i:');
$consoleColors = array(1 => "0;32", 2 => "01;31"); //green and red

try {
    $backup = Backup::factory($options['i']);
    $backup->run();
} catch (BackupException $e) {
    $exitCode = $e->getCode();
    print "\033[{$consoleColors[$exitCode]}m" . $e->getMessage() . "\033[0m\n"; //color based on the error code
} catch (Exception $e) {
    $exitCode = 3;
    print "\033[{$consoleColors[2]}m" . $e->getMessage() . "\033[0m\n"; //always red
}

exit($exitCode);

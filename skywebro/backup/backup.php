<?php
define('DIRECTORY_MASK', 0777);

spl_autoload_extensions('.class.php');
spl_autoload_register();

use Skywebro\Backup\Backup;

$exitCode = 0;
$options = getopt('i:');
$consoleColors = array(1 => "0;32", 2 => "01;31"); //green and red

try {
    $backup = Backup::factory($options['i']);
    $backup->run();
} catch (Skywebro\Backup\Exception $e) {
    $exitCode = $e->getCode();
    print "\033[{$consoleColors[$exitCode]}m" . $e->getMessage() . "\033[0m\n";
} catch (Exception $e) {
    $exitCode = 3;
    print "\033[01;31m" . $e->getMessage() . "\033[0m\n";
}

exit($exitCode);

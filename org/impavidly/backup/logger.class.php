<?php
namespace Org\Impavidly\Backup;

use Org\Impavidly\Backup\Exceptions\Exception as BackupException;

class Logger extends \Logger {
    public static function getLogger($name, $path, $from, $to) {
        self::validateEmail($from);
        self::validateEmail($to);

        $pattern = '%d{ISO8601} [%p]: %m %n';
        
        parent::configure(array(
            'rootLogger' => array(
                'appenders' => array('default', 'console', 'mail'),
            ),
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderFile',
                    'layout' => array(
                        'class' => 'LoggerLayoutPattern',
                        'params' => array(
                            'conversionPattern' => $pattern,
                        ),
                    ),
                    'params' => array(
                        'file' => "{$path}/" . strtolower($name) . ".log",
                        'append' => true
                    ),
                ),
                'console' => array(
                    'class' => 'LoggerAppenderConsole',
                    'layout' => array(
                        'class' => 'LoggerLayoutPattern',
                        'params' => array(
                            'conversionPattern' => $pattern,
                        ),
                    ),
                ),
                'mail' => array(
                    'class' => 'LoggerAppenderMail',
                    'threshold' => "WARN",
                    'layout' => array(
                        'class' => 'LoggerLayoutPattern',                        
                        'params' => array(
                            'conversionPattern' => $pattern,
                        ),
                    ),
                    'params' => array(
                        'to' => $to,
                        'from' => $from,
                        'subject' => 'PHP Backup Report',
                    ),
                ),
            )
        ));

        return parent::getLogger($name);
    }

    protected static function validateEmail($email) {
        if ((empty($email)) || (false == filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new BackupException("The ({$email}) email address is not valid");
        }
    }    
}
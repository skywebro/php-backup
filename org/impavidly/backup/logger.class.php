<?php
namespace Org\Impavidly\Backup;

class Logger extends \Logger {
    public static function getLogger($name, $path) {
        parent::configure(array(
            'rootLogger' => array(
                'appenders' => array('default', 'console'),
            ),
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderFile',
                    'layout' => array(
                        'class' => 'LoggerLayoutPattern',
                        'params' => array(
                            'conversionPattern' => '%d{ISO8601} [%p]: %m %n',
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
                            'conversionPattern' => '%d{ISO8601} [%p]: %m %n',
                        ),
                    ),
                )
            )
        ));

        return parent::getLogger($name);
    }
}
<?php
namespace Skywebro\Backup;

set_time_limit(0);

class Backup {
    protected static $instances = array();
    protected $iniFile = '';
    protected $logsPath = '';
    protected $wgetPath = '';
    protected $hosts = array();
    protected $destinationPath = '';

    public static function factory($iniFile) {
        if (empty($iniFile)) {
            throw new Exception("Usage: backup -i ini_file", 1);
        }

        self::checkFile($iniFile);

        if (!(self::$instances[$name = md5($iniFile)] instanceof Backup)) {
            self::$instances[$name] = new Backup();
            self::$instances[$name]->iniFile = $iniFile;
            self::$instances[$name]->prepare();
        }

        return self::$instances[$name];
    }

    public function run() {
    }

    protected function prepare() {
        $this->ini();
        $this->mkdir($this->logsPath);
        $this->mkdir($this->destinationPath);
    }

    protected function ini() {
        if (!($ini = @parse_ini_file($this->iniFile, true))) {
            throw new Exception('Could not parse the ini file');
        }

        $this->validate($ini);

        $this->logsPath = $ini['paths']['logs'];
        $this->wgetPath = $ini['paths']['wget'];
        $this->destinationPath = $ini['paths']['destination'];

        foreach($ini['hosts'] as $hosts) {
            $this->checkFile($hosts);
            $this->hosts[] = $hosts;
        }
    }

    protected function validate($ini) {
        static $values = array('logs', 'wget', 'destination');

        if (!is_array($ini['hosts'])) {
            throw new Exception('The [hosts] section is not defined in the ini file');
        }

        if (!is_array($ini['paths'])) {
            throw new Exception('The [paths] section is not defined in the ini file');
        }

        foreach($values as $value) {
            if (!array_key_exists($value, $ini['paths'])) {
                throw new Exception("{$value} is not defined in the ini file");
            }
        }

        $this->checkFile($ini['paths']['wget']) && $this->checkExecutable($ini['paths']['wget']);

        return true;
    }

    protected function mkdir($dir) {
        if (is_dir($dir)) {
            if (!is_writable($dir)) {
                throw new Exception("$dir is not writable");
            }
        } elseif (!@mkdir($dir, DIRECTORY_MASK, true)) {
            throw new Exception("Could not create $dir");
        }
    }

    protected function checkFile($name) {
        if (!is_file($name) || !is_readable($name)) {
            throw new Exception("$name does not exist or it's not readable");
        }
    }

    protected function checkExecutable($cmd) {
        if (!is_executable($this->wgetPath)) {
            throw new Exception("$cmd is not executable");
        }
    }
}
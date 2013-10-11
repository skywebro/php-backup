<?php
set_time_limit(0);

class Backup {
    protected static $instances = array();
    protected $iniFile = '';
    protected $logsPath = '';
    protected $wgetPath = '';
    protected $hostsPath = '';
    protected $destinationPath = '';

    public static function factory($iniFile) {
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
        $this->parseIni();
        $this->checkFile($this->hostsPath);
        $this->checkFile($this->wgetPath) && $this->checkExecutable($this->wgetPath);
        $this->mkdir($this->logsPath);
        $this->mkdir($this->destinationPath);
    }

    protected function parseIni() {
        if (!($ini = @parse_ini_file($this->iniFile, true))) {
            throw new Exception('Could not parse the ini file');
        }
        $this->logsPath = $ini['paths']['logs'];
        $this->wgetPath = $ini['paths']['wget'];
        $this->hostsPath = $ini['paths']['hosts'];
        $this->destinationPath = $ini['paths']['destination'];
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
            throw new Exception("$name is not a file or it's not readable");
        }
    }

    protected function checkExecutable($cmd) {
        if (!is_executable($this->wgetPath)) {
            throw new Exception("$cmd is not executable");
        }
    }
}
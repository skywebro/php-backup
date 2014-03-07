<?php
namespace Org\Impavidly\Backup;

use Org\Impavidly\Backup\Exceptions\Exception as BackupException;

set_time_limit(0);

class Backup {
    protected static $instances = array();
    protected $iniFile = '';
    protected $wgetPath = '';
    protected $mysqlDumpPath = '';
    protected $hosts = array();
    protected $destinationPath = '';
    protected $outputPath = '';
    protected $retries = 3;
    protected $logger = null;

    public static function factory($iniFile) {
        if (empty($iniFile)) {
            throw new BackupException("Usage: backup -i ini_file", 1);
        }

        self::checkFile($iniFile);

        if (!(self::$instances[$name = md5($iniFile)] instanceof Backup)) {
            self::$instances[$name] = new Backup();
            self::$instances[$name]->iniFile = $iniFile;
            self::$instances[$name]->prepare();
            self::$instances[$name]->logger = Logger::getLogger('backup');
        }

        return self::$instances[$name];
    }

    public function run() {
        foreach ($this->hosts as $hostsFile) {
            $lineNumber = 1;
            if (false !== ($handle = fopen($hostsFile, "r"))) {
                while (false !== ($data = fgetcsv($handle, 1024, ","))) {
                    $cfg = array(
                        'ftpHost' => $data[0],
                        'ftpUsername' => $data[1],
                        'ftpPassword' => $data[2],
                        'ftpPath' => $data[3],
                        'mysqlHost' => $data[4],
                        'mysqlDatabase' => $data[5],
                        'mysqlUser' => $data[6],
                        'mysqlPassword' => $data[7],
                        'outputPath' => $this->outputPath,
                        'destinationPath' => $this->destinationPath,
                        'wgetPath' => $this->wgetPath,
                        'mysqlDumpPath' => $this->mysqlDumpPath,
                        'retries' => $this->retries,
                        'logger' => $this->logger,
                        'hostsFile' => $hostsFile,
                        'lineNumber' => $lineNumber++,
                    );
                    $task = new Task($cfg);
                    $task->run();
                    unset($task); //free the instance
                }
                fclose($handle);
            }
        }
    }

    protected function prepare() {
        $this->ini();
        $this->mkdir($this->destinationPath);

        $this->outputPath = $this->destinationPath . '/' . date("Ymd");
        $this->mkdir($this->outputPath);
    }

    protected function ini() {
        if (!($ini = @parse_ini_file($this->iniFile, true))) {
            throw new BackupException('Could not parse the ini file');
        }

        $this->validate($ini);

        $this->wgetPath = $ini['paths']['wget'];
        $this->mysqlDumpPath = $ini['paths']['mysqldump'];
        $this->destinationPath = $ini['paths']['destination'];
        $this->retries = (int)$ini['general']['retries'];

        foreach($ini['hosts'] as $hosts) {
            $this->checkFile($hosts);
            $this->hosts[] = $hosts;
        }
    }

    protected function validate($ini) {
        static $values = array('wget', 'mysqldump', 'destination');

        if (!is_array($ini['hosts'])) {
            throw new BackupException('The [hosts] section is not defined in the ini file');
        }

        if (!is_array($ini['paths'])) {
            throw new BackupException('The [paths] section is not defined in the ini file');
        }

        foreach($values as $value) {
            if (!array_key_exists($value, $ini['paths'])) {
                throw new BackupException("{$value} is not defined in the ini file");
            }
        }

        $this->checkFile($ini['paths']['wget']) && $this->checkExecutable($ini['paths']['wget']);
        $this->checkFile($ini['paths']['mysqldump']) && $this->checkExecutable($ini['paths']['mysqldump']);

        return true;
    }

    protected function mkdir($dir) {
        if (is_dir($dir)) {
            if (!is_writable($dir)) {
                throw new BackupException("$dir is not writable");
            }
        } elseif (!@mkdir($dir, DIRECTORY_MASK, true)) {
            throw new BackupException("Could not create $dir");
        }
    }

    protected function checkFile($name) {
        if (!is_file($name) || !is_readable($name)) {
            throw new BackupException("$name does not exist or it's not readable");
        }
    }

    protected function checkExecutable($cmd) {
        if (!is_executable($this->wgetPath)) {
            throw new BackupException("$cmd is not executable");
        }
    }
}
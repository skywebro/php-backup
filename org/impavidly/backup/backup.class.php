<?php
namespace Org\Impavidly\Backup;

use Org\Impavidly\Backup\Exceptions\Exception as BackupException;

set_time_limit(0);

class Backup {
    protected static $instances = array();
    protected $iniFileName = '';
    protected $config = array();
    protected $wgetPath = '';
    protected $mysqlDumpPath = '';
    protected $hosts = array();
    protected $destinationPath = '';
    protected $outputPath = '';
    protected $retries = 3;
    protected $logger = null;
    protected $loggerFromEmail = '';
    protected $loggerToEmail = '';    
    protected $observers = array();
    protected $fieldCount = 0;
    protected $custom = array();

    public static function factory($iniFileName) {
        if (empty($iniFileName)) {
            throw new BackupException("Usage: backup -i ini_file", 1);
        }

        self::checkFile($iniFileName);

        if (!(self::$instances[$name = md5($iniFileName)] instanceof Backup)) {
            self::$instances[$name] = new Backup();
            self::$instances[$name]->iniFileName = $iniFileName;
            self::$instances[$name]->prepare();
        }

        return self::$instances[$name];
    }

    public function run() {
        foreach ($this->hosts as $hostsFile) {
            $lineNumber = 1;
            if (false !== ($handle = fopen($hostsFile, "r"))) {
                while (false !== ($data = fgetcsv($handle, 1024, ","))) {
                    if ($this->fieldCount != count($data)) {
                        $this->logger->warn("Line {$lineNumber} from '{$hostsFile}' does not have {$this->fieldCount} fields, skipping.");
                        $lineNumber++;
                        continue;
                    }
                    $cfg = array(
                        'data' => $data,
                        'config' => $this->config,
                        'observerClasses' => $this->observers,
                        'outputPath' => $this->outputPath,
                        'destinationPath' => $this->destinationPath,
                        'retries' => $this->retries,
                        'logger' => $this->logger,
                        'loggerFromEmail' => $this->loggerFromEmail,
                        'loggerToEmail' => $this->loggerToEmail,
                        'hostsFile' => $hostsFile,
                        'lineNumber' => $lineNumber++,
                    );
                    $task = new Task($cfg);
                    $task->run();
                    unset($task); //free the instance
                }
                fclose($handle);
            } else {
                $this->logger->error("Could not process the hosts file {$hostsFile}.");
            }
        }
    }

    protected function prepare() {
        $this->ini();
    }

    protected function ini() {
        if (!($ini = @parse_ini_file($this->iniFileName, true))) {
            throw new BackupException('Could not parse the ini file');
        }

        $this->validate($ini);

        $this->destinationPath = $ini['general']['destination'];
        $this->outputPath = $this->destinationPath . '/' . date("Ymd");
        $this->fieldCount = (int)$ini['general']['hosts_field_count'];
        $this->retries = max($ini['general']['retries'], 1);
        $this->loggerFromEmail = $ini['general']['email_from'];
        $this->loggerToEmail = $ini['general']['email_to'];
        
        $this->mkdir($this->destinationPath);
        $this->mkdir($this->outputPath);

        $this->logger = Logger::getLogger('backup', $this->outputPath, $this->loggerFromEmail, $this->loggerToEmail);

        foreach($ini['observers'] as $name => $class) {
            if (class_exists($class)) {
                $this->observers[$name] = $class;
            } else {
                throw new BackupException("The observer class '{$class}' was not found");
            }
        }

        foreach($ini['hosts'] as $hosts) {
            $this->checkFile($hosts);
            $this->hosts[] = $hosts;
        }
        
        $this->config = $ini;
    }

    protected function validate($ini) {
        if (!is_array($ini['general'])) {
            throw new BackupException('The [general] section is not defined in the ini file');
        }

        if (!is_array($ini['observers'])) {
            throw new BackupException('The [observers] section is not defined in the ini file');
        }

        if (!is_array($ini['hosts'])) {
            throw new BackupException('The [hosts] section is not defined in the ini file');
        }

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
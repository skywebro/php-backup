<?php
namespace Skywebro\Backup;

class Task {
    protected $ftpHost = '';
    protected $ftpUsername = '';
    protected $ftpPassword = '';
    protected $mysqlHost = '';
    protected $mysqlDatabase = '';
    protected $mysqlUser = '';
    protected $mysqlPassword = '';
    protected $logsPath = '';
    protected $outputPath = '';
    protected $destinationPath = '';
    protected $wgetPath = '';
    protected $mysqldumpPath = '';

    public function __construct($cfg) {
        $this->ftpHost = $cfg['ftpHost'];
        $this->ftpUsername = $cfg['ftpUsername'];
        $this->ftpPassword = $cfg['ftpPassword'];
        $this->mysqlHost = $cfg['mysqlHost'];
        $this->mysqlDatabase = $cfg['mysqlDatabase'];
        $this->mysqlUser = $cfg['mysqlUser'];
        $this->mysqlPassword = $cfg['mysqlPassword'];
        $this->logsPath = $cfg['logsPath'];
        $this->outputPath = $cfg['outputPath'];
        $this->destinationPath = $cfg['destinationPath'];
        $this->wgetPath = $cfg['wgetPath'];
        $this->mysqldumpPath = $cfg['mysqldumpPath'];
    }

    public function run() {
        $this->wget();
        $this->mysqlDump();
    }

    protected function wget() {
        $command = "{$this->wgetPath} --continue --mirror --directory-prefix={$this->destinationPath} ftp://{$this->ftpUsername}:{$this->ftpPassword}@{$this->ftpHost}/httpdocs/ 2> {$this->logsPath}/{$this->ftpHost}.log";
        system($command, $status);
        /*
        if (false !== ($wgetHandle = popen($command, 'r'))) {
            if (false !== ($outputHandle = fopen("{$this->logsPath}/{$host}.log", "w+"))) {
                while ($read = fgets($wgetHandle)) {
                    fputs($outputHandle, $read);
                }
                fclose($outputHandle);
            }
            pclose($wgetHandle);
        }
        */
    }

    protected function mysqlDump() {
        $command = "{$this->mysqldumpPath} -h {$this->mysqlHost} -u {$this->mysqlUser} --password={$this->mysqlPassword} {$this->mysqlDatabase} > {$this->outputPath}/{$this->mysqlHost}_{$this->mysqlDatabase}.sql";
        system($command, $status);
    }
}
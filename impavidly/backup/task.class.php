<?php
namespace Impavidly\Backup;

class Task {
    public function __construct($cfg) {
        foreach($cfg as $key => $value) {
            $this->{$key} = $value;
        }
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
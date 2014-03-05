<?php
namespace Impavidly\Backup\Observers;

class MysqlDump implements \SplObserver {
    public function update(\SplSubject $subject) {
        $command = "{$subject->mysqldumpPath} -h {$subject->mysqlHost} -u {$subject->mysqlUser} --password={$subject->mysqlPassword} {$subject->mysqlDatabase} > {$subject->outputPath}/{$subject->mysqlHost}_{$subject->mysqlDatabase}.sql";
        system($command, $status);

        return $status;
    }
}
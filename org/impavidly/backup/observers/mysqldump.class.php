<?php
namespace Org\Impavidly\Backup\Observers;

class MysqlDump extends Common {
    protected $name = 'MySQLDump Observer';

    public function update(\SplSubject $subject) {
        $command = "{$subject->mysqlDumpPath} -P {$subject->data[6]} -h {$subject->data[5]} -u {$subject->data[8]} --password={$subject->data[9]} {$subject->data[7]} > {$subject->outputPath}/{$subject->data[5]}_{$subject->data[7]}.sql";
        $status = $this->execute($subject, $command);

        return $status;
    }
}
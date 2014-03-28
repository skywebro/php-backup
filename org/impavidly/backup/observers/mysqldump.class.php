<?php
namespace Org\Impavidly\Backup\Observers;

class MysqlDump extends Common {
    protected $name = 'MySQLDump Observer';

    public function update(\SplSubject $subject) {
        $data = $this->getCsvRecord($subject->data, $subject->config['mysqldump']['csv_fields_indexes']);        
        $command = "{$subject->config['mysqldump']['mysqldump']} -P {$data[1]} -h {$data[0]} -u {$data[3]} --password={$data[4]} {$data[2]} > {$subject->outputPath}/{$data[0]}_{$data[2]}.sql";
        $status = $this->execute($subject, $command);

        return $status;
    }
}
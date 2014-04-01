<?php
namespace Org\Impavidly\Backup\Observers;

class Ssh extends Common {
    protected $name = 'SSH Observer';

    public function update(\SplSubject $subject) {
        $data = $this->getCsvRecord($subject->data, $subject->config['ssh']['csv_fields_indexes']);
        $command = "{$subject->config['ssh']['sshpass']} -p '{$data[3]}' {$subject->config['ssh']['scp']} -r -P {$data[1]} {$data[2]}@{$data[0]}:{$data[4]} {$subject->outputPath}";
        $status = $this->execute($subject, $command);

        return $status;
    }
}
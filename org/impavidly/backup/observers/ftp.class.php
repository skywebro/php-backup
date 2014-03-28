<?php
namespace Org\Impavidly\Backup\Observers;

class Ftp extends Common {
    protected $name = 'FTP Observer';

    public function update(\SplSubject $subject) {
        $data = $this->getCsvRecord($subject->data, $subject->config['ftp']['csv_fields_indexes']);
        $command = "{$subject->config['ftp']['wget']} --continue --mirror --directory-prefix={$subject->outputPath} ftp://{$data[2]}:{$data[3]}@{$data[0]}:{$data[1]}/{$data[4]} 2> {$subject->outputPath}/{$data[0]}_{$data[2]}.log";
        $status = $this->execute($subject, $command);

        return $status;
    }
}
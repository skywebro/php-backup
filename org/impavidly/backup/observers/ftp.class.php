<?php
namespace Org\Impavidly\Backup\Observers;

class Ftp extends Common {
    protected $name = 'FTP Observer';

    public function update(\SplSubject $subject) {
        $command = "{$subject->wgetPath} --continue --mirror --directory-prefix={$subject->outputPath} ftp://{$subject->data[2]}:{$subject->data[3]}@{$subject->data[0]}:{$subject->data[1]}/{$subject->data[4]} 2> {$subject->outputPath}/{$subject->data[0]}_{$subject->data[2]}.log";
        $status = $this->execute($subject, $command);

        return $status;
    }
}
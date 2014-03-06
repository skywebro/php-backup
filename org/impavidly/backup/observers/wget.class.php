<?php
namespace Org\Impavidly\Backup\Observers;

class Wget extends Common {
    public function update(\SplSubject $subject) {
        $command = "{$subject->wgetPath} --continue --mirror --directory-prefix={$subject->destinationPath} ftp://{$subject->ftpUsername}:{$subject->ftpPassword}@{$subject->ftpHost}/httpdocs/ 2> {$subject->logsPath}/{$subject->ftpHost}.log";
        $status = $this->execute($subject, $command);

        return $status;
    }
}
<?php
namespace Org\Impavidly\Backup\Observers;

class Wget implements \SplObserver {
    public function update(\SplSubject $subject) {
        $command = "{$subject->wgetPath} --continue --mirror --directory-prefix={$subject->destinationPath} ftp://{$subject->ftpUsername}:{$subject->ftpPassword}@{$subject->ftpHost}/httpdocs/ 2> {$subject->logsPath}/{$subject->ftpHost}.log";
        system($command, $status);

        return $status;
        /*
        if (false !== ($wgetHandle = popen($command, 'r'))) {
            if (false !== ($outputHandle = fopen("{$subject->logsPath}/{$host}.log", "w+"))) {
                while ($read = fgets($wgetHandle)) {
                    fputs($outputHandle, $read);
                }
                fclose($outputHandle);
            }
            pclose($wgetHandle);
        }
        */
    }
}
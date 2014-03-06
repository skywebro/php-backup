<?php
namespace Org\Impavidly\Backup\Observers;

use Org\Impavidly\Backup\Exception;

class Common implements \SplObserver {
    public function update(\SplSubject $subject) {
        throw new Exception("Not implemented.");
    }

    public function execute(\SplSubject $subject, $command) {
        $retries = 0;
        $status = -1;

        do {
            system($command, $status);
            if (0 == $status) break;
        } while (++$retries <= $subject->retries);

        if ($retries > $subject->retries) {
            throw new Fail_Exception("Maximum number of retries reached, giving up.");
        }

        return $status;
    }
}
<?php
namespace Org\Impavidly\Backup\Observers;

use Org\Impavidly\Backup\Exceptions\Exception as BackupException;
use Org\Impavidly\Backup\Exceptions\Fail_Exception;

abstract class Common implements \SplObserver {
    protected $name = 'Not implemented';

    public function getCsvRecord($data, $indexes) {
        $indexesArray = explode(',', $indexes);
        $return = array();

        foreach($indexesArray as $index) {
            $return[] = $data[trim($index)];
        }
        
        return $return;
    }
    
    public function update(\SplSubject $subject) {
        throw new BackupException('Not implemented.');
    }

    public function execute(\SplSubject $subject, $command) {
        $retries = 1;
        $status = -1;

        do {
            $subject->logger->info("{$this->name}: executing line #{$subject->lineNumber} from file '{$subject->csvFile}' retry #{$retries}");
            system($command, $status);
            if (0 == $status) {
                $subject->logger->info("{$this->name}: done executing line #{$subject->lineNumber} from file '{$subject->csvFile}'");
                break;
            }
        } while (++$retries <= $subject->retries);

        if ($retries > $subject->retries) {
            $error = $this->name . ': maximum number of retries (' . $subject->retries . ') reached in "' . $subject->csvFile . '" line #' . $subject->lineNumber . ', giving up.';
            $subject->logger->error($error);
            throw new Fail_Exception($error);
        }

        return $status;
    }
}
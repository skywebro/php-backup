<?php
namespace Org\Impavidly\Backup\Observers;

use Org\Impavidly\Backup\Exceptions\Fail_Exception;

class Directory extends Common {
    protected $name = 'Directory Observer';

    public function update(\SplSubject $subject) {
        $data = $this->getCsvRecord($subject->data, $subject->config['directory']['csv_fields_indexes']);
        $status = -1;
                
        try {
            if (!is_dir($data[0]) || (!is_readable($data[0]))) {
                $msg = "{$this->name}: Could not find/read path ({$data[0]}), skipping.";
                throw new Fail_Exception($msg);
            }
            
            $archiveName = $subject->outputPath . DIRECTORY_SEPARATOR . str_replace(DIRECTORY_SEPARATOR, '_', trim($data[0], DIRECTORY_SEPARATOR)) . '.tar.bz2';
            $command = "{$subject->config['directory']['tar']} -jcf $archiveName --directory=" . $data[0] . " .";
            $subject->logger->info("{$this->name}: running {$command}");
            $status = $this->execute($subject, $command);
        } catch (Fail_Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }

        return $status;
    }
}
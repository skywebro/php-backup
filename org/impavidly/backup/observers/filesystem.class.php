<?php
namespace Org\Impavidly\Backup\Observers;

class Filesystem extends Common {
    protected $name = 'Filesystem Observer';

    public function update(\SplSubject $subject) {
        $data = $this->getCsvRecord($subject->data, $subject->config['filesystem']['csv_fields_indexes']);
        $path = $data[0];
        $status = 0;
        
        try {
            $directory = new \DirectoryIterator($path);
            while ($directory->valid()) {
                if (!$directory->isDot() && ($directory->isDir())) {
                    try {
                        $archiveName = $subject->outputPath . DIRECTORY_SEPARATOR . str_replace(DIRECTORY_SEPARATOR, '_', trim($directory->getPathname(), DIRECTORY_SEPARATOR)) . '_' . $directory->getFilename() . '.tar.bz2';
                        $command = "{$subject->config['filesystem']['tar']} -jcf $archiveName --directory=" . $directory->getPathname() . " .";
                        $subject->logger->info("{$this->name}: running {$command}");
                        $this->execute($subject, $command);
                    } catch (\Exception $e) {
                        $status = -1;
                    }
                }
                $directory->next();
            }
        } catch (\UnexpectedValueException $e) {
            $subject->logger->warn("{$this->name}: could not open ($path).");
            $status = -1;
            
        } catch (\Exception $e) {
            $subject->logger->warn("{$this->name}: unknown error.");
            $status = -1;
        }

        return $status;
    }
}
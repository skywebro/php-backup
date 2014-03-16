<?php
namespace Org\Impavidly\Backup\Observers;

class Filesystem extends Common {
    protected $name = 'Filesystem Observer';

    public function update(\SplSubject $subject) {
        $path = $subject->data[0];
        $directory = new \FilesystemIterator($path);
        $status = 0;
        
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

        return $status;
    }
}
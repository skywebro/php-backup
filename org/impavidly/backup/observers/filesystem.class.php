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
                    $command = "{$subject->config['filesystem']['tar']} -jcf {$subject->outputPath}/" . $directory->getFilename() . ".tar.bz2 --directory=" . $directory->getPathname() . " .";
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
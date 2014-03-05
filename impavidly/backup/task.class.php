<?php
namespace Impavidly\Backup;

use Impavidly\Backup\Observers\Wget;
use Impavidly\Backup\Observers\MysqlDump;

class Task implements \SplSubject {
    protected $observers = array();

    public function __construct($cfg) {
        foreach($cfg as $key => $value) {
            $this->{$key} = $value;
        }

        $this->attach(new Wget());
        $this->attach(new MysqlDump());
    }

    public function attach(\SplObserver $observer) {
        $this->observers[] = $observer;
    }

    public function detach(\SplObserver $observer) {
        $key = array_search($observer, $this->observers, true);
        if ($key) {
            unset($this->observers[$key]);
        }
    }

    public function notify() {
        foreach ($this->observers as $i => $observer) {
            //launch every observer of the current task in its own process
            $pid = pcntl_fork();
            if (-1 == $pid) {
                throw new Exception('Could not fork the task in the background.');
            } elseif (0 == $pid) {
                //in the child, update the observer
                $status = $observer->update($this);
                exit($status);
            } else {
                //in the parent, do nothing
            }
        }

        //wait for the observers to finish
        $status = 0;
        while (-1 != pcntl_waitpid(0, $status)) {
            $status = pcntl_wexitstatus($status);
        }

        return $status;
    }

    public function run() {
        return $this->notify();
    }
}
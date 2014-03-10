<?php
namespace Org\Impavidly\Backup;

use Org\Impavidly\Backup\Exceptions\Fork_Exception;
use Org\Impavidly\Backup\Exceptions\Fail_Exception;

class Task implements \SplSubject {
    protected $observers = array();

    public function __construct($cfg) {
        foreach($cfg as $key => $value) {
            $this->{$key} = $value;
        }
        $this->attachObservers();
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
        foreach ($this->observers as $observer) {
            //launch every observer of the current task in its own process
            try {
                $pid = pcntl_fork();
                if (-1 == $pid) {
                    throw new Fork_Exception('Could not fork the task in the background.');
                } elseif (0 == $pid) {
                    //in the child, update the observer
                    $status = $observer->update($this); //throws Fail_Exception
                    exit($status);
                } else {
                    //in the parent, do nothing
                }
            } catch (Fork_Exception $e) {
                //fork failed so run the observer inside the main process
                $observer->update($this);
            } catch (Fail_Exception $e) {
                //the observer failed to update, still in child process, exiting
                $this->logger->error($e->getMessage());
                exit($e->getCode());
            }
        }

        //wait for the observers to finish
        while (-1 != pcntl_waitpid(0, $status)) {
            $status = pcntl_wexitstatus($status);
        }

        return $status;
    }

    public function run() {
        return $this->notify();
    }

    protected function attachObservers() {
        foreach($this->observerClasses as $class) {
            if (class_exists($class)) {
                $this->attach($observer = new $class());
            } else {
                $this->logger->warn("The observer class '{$class}' was not found, skipping.");
            }
        }
    }
}
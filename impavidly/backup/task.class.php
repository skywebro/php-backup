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
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function run() {
        $this->notify();
    }
}
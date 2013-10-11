<?php
namespace Skywebro\Backup;

class Exception extends \Exception {
    public function __construct($message, $code = 1) {
        parent::__construct($message);
        $this->code = $code;
    }
}
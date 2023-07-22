<?php

namespace LiveControls\Permissions\Exceptions;

use Exception;
use Throwable;

class InvalidUserPermissionException extends Exception
{
    public function __construct(string $userPermission, $code = 0, Throwable $previous = null) {
        parent::__construct('Invalid User Permission "'.$userPermission.'"', $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
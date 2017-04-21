<?php


namespace Mouf\Integration\Drupal\Druplash;

use Exception;

/**
 * This class wraps a PDO exception in order to bypass watchdog checks and have the error logged correctly.
 */
class PDOWrappedException extends \Exception
{
    public function __construct(\PDOException $previous)
    {
        parent::__construct($previous->getMessage() . PHP_EOL . $previous->getTraceAsString(), $previous->getCode(), $previous);
    }
}

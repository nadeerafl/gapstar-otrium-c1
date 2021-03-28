<?php
declare(strict_types=1);

namespace App\Exception;


use Throwable;

class DailyTurnoverDataNotAvailableException extends \Exception
{
    public function __construct($message = "Turnover data not available! for the given date range", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
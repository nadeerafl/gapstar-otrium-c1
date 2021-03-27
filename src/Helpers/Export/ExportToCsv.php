<?php
declare(strict_types=1);

namespace App\Helpers\Export;

use App\Exception\TurnoverDataNotFoundException;
use App\Query\GetTurnoverByDateRangeQuery;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use App\Config;
use App\Helpers\Export\ExportAbstract;

class ExportToCsv extends ExportAbstract
{

    /**
     * @param string $filename
     * @param array $rows
     * @param array $columns
     */
    public function write(string $filename, array $rows, array $columns)
    {
        $file = fopen($filename, 'w');
        fputcsv($file, $columns);
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }



    
}
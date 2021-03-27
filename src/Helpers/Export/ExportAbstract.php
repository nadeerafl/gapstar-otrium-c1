<?php
declare(strict_types=1);
namespace App\Helpers\Export;


abstract class ExportAbstract
{

    /**
     * @param string $filename
     * @param array $rows
     * @param array $columns
     */
    abstract public function write(string $filename, array $rows, array $columns);
}
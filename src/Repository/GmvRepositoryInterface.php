<?php
namespace App\Repository;

use DateTime;

interface GmvRepositoryInterface
{
    public function searchByDateRange(DateTime $from, DateTime $to);

}
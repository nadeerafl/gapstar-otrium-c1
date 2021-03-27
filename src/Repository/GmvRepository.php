<?php
declare(strict_types=1);

namespace App\Repository;

use DateTime;

class GmvRepository implements GmvRepositoryInterface
{
    private $table = 'gmv';

    /**
     * @var PDO
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function searchByDateRange(DateTime $from, DateTime $to): array
    {
        $query = "SELECT * FROM $this->table INNER JOIN brands on gmv.brand_id = brands.id WHERE date BETWEEN ? AND ? ORDER BY brands.id ,date asc;";

        $statement = $this->connection->prepare($query);

        $statement->execute([$from->format("Y-m-d H:i:s"), $to->format("Y-m-d H:i:s")]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

}
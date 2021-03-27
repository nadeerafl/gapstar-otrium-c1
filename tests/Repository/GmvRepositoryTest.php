<?php
declare(strict_types=1);
namespace Test\Controllers;

use App\Repository\GmvRepository;
use DateTime;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GmvRepositoryTest extends TestCase
{
     /**
     * @var PDO|MockObject
     */
    private $mock_PDO;

    /**
     * @var GmvRepository
     */
    private $gmv_repository;

    public function setUp(): void
    {
        $this->mock_PDO = $this->createMock(PDO::class);
        $this->gmv_repository = new GmvRepository($this->mock_PDO);
        parent::setUp();
    }

    public function testShouldReceiveTurnoversPerGivenDateRange()
    {
        $mock_statement = $this->createMock(PDOStatement::class);
        $expected_query = 'SELECT * FROM gmv INNER JOIN brands on gmv.brand_id = brands.id WHERE date BETWEEN ? AND ? ORDER BY brands.id ,date asc;';

        $this->mock_PDO
            ->expects($this->once())
            ->method('prepare')
            ->with($expected_query)
            ->willReturn($mock_statement);

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with(['2018-05-01 00:00:00', '2018-05-07 00:00:00']);

        $mock_statement->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);


        $this->gmv_repository->searchByDateRange(
            new DateTime('2018-05-01'),
            new DateTime('2018-05-07')
        );
    }

}
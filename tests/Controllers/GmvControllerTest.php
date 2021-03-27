<?php
declare(strict_types=1);
namespace Test\Controllers;

use App\Config;
use App\Controllers\GmvController;
use DateTime;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Repository\GmvRepositoryInterface;
use App\Helpers\Export\ExportToCsv;

class GmvControllerTest extends TestCase
{
    /**
     * @var GmvRepositoryInterface
     */
    private $mock_gmv_repository;

    /**
     * @var ExportToCsv
     */
    private $mock_export_to_csv;

    /**
     * @var GmvController
     */
    private $gmv_controller;

    public function setUp(): void
    {

        $this->mock_gmv_repository  = $this->createMock(GmvRepositoryInterface::class);
        $this->mock_export_to_csv   = $this->createMock(ExportToCsv::class);

        $this->mock_gmv_repository->method('searchByDateRange')
            ->willReturn([
                [
                    'brand_id' => 1,
                    "name" => 'brand_one',
                    "turnover" => 213231.23,
                    "date" => "2018-05-06"
                ],
                [
                    'brand_id' => 1,
                    "name" => 'brand_one',
                    "turnover" => 34234.23,
                    "date" => "2018-05-04"
                ],
            ]);

        $this->gmv_controller   = new GmvController($this->mock_gmv_repository, $this->mock_export_to_csv);
        parent::setUp();
    }

    //Test getTurnoverByDateRange function
    public function testShouldGetTurnoverArrayWithKeyAsBrandId()
    {
        $result = $this->gmv_controller->getTurnoverByDateRange(
            new DateTime('2018-05-01'),
            new DateTime('2018-05-07')
        );

        $this->assertEquals([[
            'brandId' => 1,
            'brandName' => 'brand_one',
            'total' => 'usd 247.465,46',
            'totalVatExc' => 'usd 1.178.406,95',
            'perDay' => [
                '2018-05-06' => 'usd 213.231,23',
                '2018-05-04' => 'usd 34.234,23'
            ]
        ]], $result);
    }

    // 
    public function testShouldGetTaxExcludedAmount()
    {
        $result = $this->gmv_controller->getTurnoverByDateRange(
            new DateTime('2018-05-01'),
            new DateTime('2018-05-07')
        );

        $this->assertEquals('usd 1.178.406,95', current($result)['totalVatExc']);
    }

}
?>
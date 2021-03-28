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
use Exception;

use App\Exception\DailyTurnoverDataNotAvailableException;


class GmvControllerTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;
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

    /**
     * @var DataTime
     */
    private $from_date;

    /**
     * @var DataTime
     */
    private $to_date;

     /**
     * @var array[]
     */
    private $sampleTurnovers = [
        [
            'brand_id' => 1,
            'brandName' => "brand name",
            'perDay' => [
                "2018-05-01" => 11111.11,
                "2018-05-02" => 22222.22,
            ],
            'total' => 0,
            'totalVatExc' => 0,
            'turnover'  => 535
        ]
    ];


    public function setUp(): void
    {
        $this->from_date    = new DateTime('2018-05-1');
        $this->to_date      = new DateTime('2018-05-7');

        $this->mock_gmv_repository  = $this->createMock(GmvRepositoryInterface::class);
        $this->mock_export_to_csv   = $this->createMock(ExportToCsv::class);

        $this->gmv_controller   = new GmvController($this->mock_gmv_repository, $this->mock_export_to_csv);
        parent::setUp();
    }

    /**
     * Get mocked returned value from DB query
     * 
     */
    private function getTurnoverRow()
    {

        $this->mock_gmv_repository->method('searchByDateRange')
        ->willReturn([
            [
                'id' => 1,
                "brand_id" => '1',
                "date" => "2018-05-01 00:00:00",
                "turnover" => "10633.26",
                "name" => "O-Brand",

            ]
        ]);
    }

    /**
     * get Mocked formatted data
     */
    private function getMockedTurnovers()
    {
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
    }

    //Test getTurnoverByDateRange function
    public function testShouldGetTurnoverArrayWithKeyAsBrandId()
    {
        $this->getMockedTurnovers();

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
        $this->getMockedTurnovers();
        $result = $this->gmv_controller->getTurnoverByDateRange(
            new DateTime('2018-05-01'),
            new DateTime('2018-05-07')
        );

        $this->assertEquals('usd 1.178.406,95', current($result)['totalVatExc']);
    }

    
    public function testShouldThrowExceptionWhenThereIsNoData(): void
    {
        $this->mock_gmv_repository
            ->expects($this->once())
            ->method('searchByDateRange')
            ->with(
                $this->from_date,
                $this->to_date
            )
            ->willReturn([]);

        $this->expectException(DailyTurnoverDataNotAvailableException::class);


            $this->gmv_controller->getTurnoverByDateRange(
                $this->from_date,
                $this->to_date
            );
        
 
       
    }

    public function testCsvColumnGeneratedByGivenDatePeriod(): void
    {
        $this->getTurnoverRow();

        $this->getFunctionMock(__NAMESPACE__, "fopen");
        $fputcsv = $this->getFunctionMock(__NAMESPACE__, "fputcsv");
        $this->getFunctionMock(__NAMESPACE__, "fclose");

        $fputcsv->expects($this->any())
            ->withConsecutive([null, [
                'Brand Id',
                'Brand Name',
                '2018-05-01',
                '2018-05-02',
                '2018-05-03',
                '2018-05-04',
                '2018-05-05',
                '2018-05-06',
                '2018-05-07',
                'Total Turnover',
                'Total Turnover (VAT Excluded)'
            ]], [null, [
                1, 'sample brand', 'usd 23.243,23', 'usd 89.723,23', 'usd 0,00', 'usd 0,00',]
            ])
            ->willReturn(1);


        $this->gmv_controller->generateDailyTurnoverReport(
            $this->from_date,
            $this->to_date
        );
    }

}
?>
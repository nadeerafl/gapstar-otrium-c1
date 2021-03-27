<?php 
namespace App\Controllers;
use App\Repository\GmvRepositoryInterface;
use DateTime;
use Exception;
use App\Config;
use DateInterval;
use DatePeriod;
use App\Helpers\Export\ExportToCsv;

class GmvController {

     /**
     * @var GmvRepositoryInterface
     */
    private $gmv_repository;
     /**
     * @var ExportToCsv
     */
    private $export_to_csv;

    private $tax_percentage     = 0.21;
    private $report_name_prefix = 'daily-turnover';
    private $currency           = 'usd';



    public function __construct(GmvRepositoryInterface $gmv_repository, ExportToCsv $export_to_csv)
    {
        $this->gmv_repository   = $gmv_repository;
        $this->export_to_csv    = $export_to_csv;
    }

    /**
     * Get formatted data for generate CSV
     * 
     * @param   DateTime    $from_date          Create report from date
     * @param   DateTime    $to_date            Create report to date
     * @return  array       $formatted_data     Formatted data for csv creation
     */
    public function getTurnoverByDateRange(DateTime $from_date, DateTime $to_date): array
    {
        $turnovers          = $this->gmv_repository->searchByDateRange($from_date, $to_date);
        $formatted_data     = [];
        // Setup row data array for calculations
        $row_data           = [];

        foreach ($turnovers as $turnover) 
        {
            $brand_id           = $turnover['brand_id'];
            $turnover_amount    = floatval($turnover['turnover']);
            $turnover_date      = (new DateTime($turnover['date']))->format("Y-m-d");

            if (!isset($row_data[$brand_id])) {

                $row_data[$brand_id] = [
                    'brandId'       => $brand_id,
                    'brandName'     => $turnover['name'],
                    'total'         => 0,
                    'totalVatExc'   => 0
                ];

                $formatted_data[$brand_id] = $row_data[$brand_id];


            }

            
            $formatted_data[$brand_id]['perDay'][$turnover_date]    = $this->formatCurrencyString($turnover_amount);
            // set row data
            $row_data[$brand_id]['total']                           = $this->getTotalWithTax($row_data[$brand_id]['total'], $turnover_amount);
            $row_data[$brand_id]['totalVatExc']                     = $this->getTotalWithoutTax($row_data[$brand_id]['totalVatExc'], $turnover_amount);

            // Formatted data
            $formatted_data[$brand_id]['total']                     = $this->formatCurrencyString($row_data[$brand_id]['total']);
            $formatted_data[$brand_id]['totalVatExc']               = $this->formatCurrencyString($row_data[$brand_id]['totalVatExc']);

        }

        return array_values($formatted_data);
    }

    /**
     * @param   Float   $amount
     * @return  Sting   Curency symbol added currency string
     */
    private function formatCurrencyString(float $amount): string
    {
        return $this->currency.' '. number_format($amount, 2, ',', '.');
    }

    /**
     * Calculate total
     * 
     * @param   float   $total  Current total
     * @param   float   $amount New amount to add to the total
     * @return  float   $total  New total after adding the amount
     */
    private function getTotalWithTax(float $total, float $amount) : float
    {
        return $total + $amount;
    }

    /**
     * get un taxted amount
     * 
     * @param   float   $tax_added_amount   Tax included amount
     * @return  float   $un_taxted_amount   Tax excluded amount
     */
    private function getAmountWithoutTax(float $tax_added_amount) : float
    {
        $un_taxted_amount   = $tax_added_amount / $this->tax_percentage;
        return $un_taxted_amount;
    }

    /**
     * get total without tax
     * 
     * @param   float   $un_taxted_total    Tax included amount
     * @param   float   $taxted_amonurt     Tax included amount
     * @return  float   $un_taxted_total    Un taxted total   
     */
    private function getTotalWithoutTax(float $un_taxted_total, float $tax_added_amount) : float
    {
        $un_taxted_amount   = $this->getAmountWithoutTax($tax_added_amount);
        $un_taxted_total    = $un_taxted_total + $un_taxted_amount;
        return $un_taxted_total;
    }


    /**
     * Generate daily turnover report
     * @param   DateTime                        $from_date  Report required from date
     * @param   DateTime                        $to_date    Report required to date
     * @throws  TurnoverDataNotFoundException   
     * @throws  Exception
     */
    public function generateDailyTurnoverReport(DateTime $from_date, DateTime $to_date)
    {
        $turnovers = $this->getTurnoverByDateRange($from_date, $to_date);

        if (empty($turnovers)) {
            throw new TurnoverDataNotFoundException();
        }

        $filename   = $this->getFilename($from_date, $to_date);

        $columns    = $this->getColumns($from_date, $to_date);
        $rows       = $this->prepareDataRows($turnovers);

        $this->export_to_csv->write($filename, $rows, $columns);
    }

    /**
     * @param   DateTime    $from
     * @param   DateTime    $to
     * @return  string      File name
     */
    private function getFilename(DateTime $from, DateTime $to): string
    {
        return $this->report_name_prefix.'-' . $from->format("Y-m-d") . "-" . $to->format("Y-m-d") . ".csv";
    }

    /**
     * @param   DateTime $from
     * @param   DateTime $to
     * @return  Array
     */
    private function getColumns(DateTime $from, DateTime $to): array
    {
        $columns = ['Brand Id', 'Brand Name'];

        $range = new DatePeriod(
            $from,
            new DateInterval('P1D'),
            $to->modify('+1 day')
        );

        foreach ($range as $key => $date) {
            $columns[] = $date->format('Y-m-d');
        }

        $columns[] = 'Total Turnover';
        $columns[] = 'Total Turnover (VAT Excluded)';

        return $columns;
    }

    /**
     * Prepare row for write to csv
     * 
     * @param   Array   $data               Data to write to row
     * @return  Array   $formatted_rows     Formatted data for row
     */
    private function prepareDataRows(array $data)
    {
        $formatted_row = [];

        foreach($data as $key => $val)
        {
            $formatted_row[$key] = array(
                $val['brandId'],
                $val['brandName']
            );
            
            foreach($val['perDay'] as $date => $turnover)
            {
                array_push($formatted_row[$key], $turnover);  
            }

            array_push($formatted_row[$key],
                $val['total'],
                $val['totalVatExc']
            );
        }

        return $formatted_row;

    }






}


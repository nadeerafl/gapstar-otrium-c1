<?php 
include 'base.php';

use App\DB\Database;
use App\Model\Brand;
use App\Controllers\GmvController;
use App\Repository\GmvRepository;
use App\Helpers\Export\ExportToCsv;


$db             = Database::getInstance();
$connection     = $db->getConnection(); 

$gvm_repository = new GmvRepository($connection);
// set Export to CSV OBJ
$export_to_csv = new ExportToCsv();

$gmv_controller  = new GmvController($gvm_repository, $export_to_csv);
// $gmv_controller->generateDailyTurnoverReport(
//     new DateTime('2018-05-1'),
//     new DateTime('2018-05-7')
// );

$gmv_controller->generateDailyTurnoverReport(
    new DateTime('2018-05-1'),
    new DateTime('2018-05-7')
);

?>
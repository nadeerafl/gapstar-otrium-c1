<?php 
include 'base.php';

use App\DB\Database;
use App\Model\Brand;
use App\Controllers\GmvController;


$db         = Database::getInstance();
$connection = $db->getConnection(); 

$brand = new Brand($connection);
$brands_data = $brand->getBrands(
    new DateTime('2018-05-1'),
    new DateTime('2018-05-7')
);

print_r($brands_data);
//return $brand->getAllBrands();
/**  
//$gvmRepository = new GmvRepository($connection);

//$gmv = new GmvController();
$brands_data = $brand->getAllBrands(
    new DateTime('2018-05-1'),
    new DateTime('2018-05-7')
);
print_r($brands_data);
*/
?>
<?php
require_once "Sales.php";

$sales = new Sales();
$sales_data = $sales->getSalesReport();

header('Content-Type: application/json');
echo json_encode($sales_data);
?>

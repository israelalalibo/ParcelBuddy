<?php
global $search;
use Models\DeliveryPointSet;
use Models\Search;

require_once ('Models/DeliveryPointSet.php');
require_once ('Models/Search.php');
$view = new stdClass();
$view->pageTitle = 'Parcel Entries for Manager';
$deliverypointSet = new DeliveryPointSet();

$search = new Search();
$delivery_point_set = null;

//pagination calculation (working out number of pages of records)
$_SESSION['total_entries'] = $deliverypointSet->countAllDeliveryPoints();
$_SESSION['number_of_pages'] = ceil($_SESSION['total_entries']/$_SESSION['rows_per_page']);

//checks to see if the sInput variable has been set
//if it has been set, only show the searched parcel entries
if(isset($_SESSION['sInput'])){
    try {
        $view->deliverypointSet = $deliverypointSet->fetchSearchedDeliveryPointsM($_SESSION['sInput']);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
else{ //if the search input session variable is false, then all the delivery points are shown
    //fetches the delivery points with LIMIT per page
    $view->deliverypointSet = $deliverypointSet->fetchAllDeliveryPoints($_SESSION['rows_per_page'], $_SESSION['start']);
}
require_once("Views/Manager.phtml");



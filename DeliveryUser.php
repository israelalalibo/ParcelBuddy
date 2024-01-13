<?php

global $search;
global $user, $view;
use Models\DeliveryPointSet;

require_once ('Models/DeliveryPointSet.php');
require_once ('Models/Search.php');
require_once ('Models/User.php');

$view = new stdClass();
$view->pageTitle = 'Delivery Points';
$deliveryPointSet = new DeliveryPointSet();

//pagination calculation (working out number of pages of records)
$_SESSION['total_entries'] = $deliveryPointSet->countAllUserDeliveryPoints($_SESSION['id']);
$_SESSION['number_of_pages'] = ceil($_SESSION['total_entries']/$_SESSION['rows_per_page']);

//checks to see if the sInput variable has been set
//if it has been set, only show the searched parcel entries
if(isset($_SESSION['sInput'])){
    try {
        $view->deliveryPointSet = $deliveryPointSet->fetchSearchedDeliveryPointsD($_SESSION['sInput'], $_SESSION['id']);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
else{//if the search input session variable is false, then all the delivery points are shown
    //fetches the delivery points with LIMIT per page
    $view->deliveryPointSet = $deliveryPointSet->fetchUserDeliveryPoints($_SESSION['id'],
        $_SESSION['rows_per_page'], $_SESSION['start']);
}
require_once("Views/DeliveryUser.phtml");







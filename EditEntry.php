<?php
use Models\DeliveryPointSet;

require_once ('Models/DeliveryPointSet.php');
$view = new stdClass();
$view->pageTitle = 'Edit Entry';
$userset = new DeliveryPointSet();
$deliveryPointToBeEdited = [];

try {
    //fetches all the usernames of the deliverers and stores them in array
    $view->userset = $userset->fetchAllDeliverers();

    //stores the row of data of the specific parcel entry to be edited
    //data stored to be used in the EditEntry.phtml view
    $view->deliveryPointToBeEdited = $userset->fetchSpecificEntry($_SESSION['ID_of_entry']);
} catch (Exception $e) {
    echo $e->getMessage();
}

require_once ('Views/EditEntry.phtml');
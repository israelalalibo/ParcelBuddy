<?php
use Models\DeliveryPointSet;

require_once ('Models/DeliveryPointSet.php');
$view = new stdClass();
$view->pageTitle = 'Add Entry';
//new DeliveryPointSet object to have access to methods that will carry out sql INSERT commands
$userSet = new DeliveryPointSet();

/**
 * Fetches all the deliverers and stores them in an array
 * to be used later in the selection dropdown
 */
try {
    $view->userSet = $userSet->fetchAllDeliverers();
} catch (Exception $e) {
    echo $e->getMessage();
}

require_once("Views/AddEntry.phtml");







<?php

use Models\DeliveryPointSet;

require_once ('Models/DeliveryPointSet.php');
$view = new stdClass();
$view->pageTitle = 'Delete Entry';
//new DeliveryPointSet object to have access to methods that will carry out sql INSERT commands
$userSetD = new DeliveryPointSet();
$deliveryPointToBeEdited = null;

require_once ('Views/DeleteEntry.phtml');